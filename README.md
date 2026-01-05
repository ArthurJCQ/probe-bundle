# ArtyProbeBundle

A Symfony bundle to create and run diagnostic probes.

## Installation

Add the bundle to your project via Composer:

```bash
composer require arty/probe-bundle
```

If you are not using Symfony Flex, you will need to register the bundle manually in `config/bundles.php`:

```php
return [
    // ...
    Arty\ProbeBundle\ArtyProbeBundle::class => ['all' => true],
];
```

## Configuration

You can configure the alerting system to be notified when a probe fails. Create a configuration file at `config/packages/arty_probe.yaml`:

```yaml
arty_probe:
    alerting:
        enabled: true
        to: "admin@example.com"
        from_address: "no-reply@example.com"
        from_name: "Probe System"
        subject: "Probe Failure Alert"
        # template: "@ArtyProbe/alerting/failure.html.twig" # Optional: customize the email template
```

## Creating a Probe

To create a probe, you need to implement the `ProbeInterface` and add the `#[Probe]` attribute to your class.

### 1. Implement the Interface

A probe must implement `Arty\ProbeBundle\Model\ProbeInterface`, which requires a `check()` method returning an `int`.

### 2. Add the Attribute

The `#[Probe]` attribute registers your service as a probe and allows you to configure its behavior.

The attribute accepts the following parameters:
- `name` (required): A unique identifier for the probe
- `description` (optional): A human-readable description of what the probe checks
- `successThreshold` (optional, default: 0): The threshold for success status
- `warningThreshold` (optional, default: 1): The threshold for warning status
- `failureThreshold` (optional, default: 2): The threshold for failure status
- `notify` (optional, default: true): Whether to send alerts when the probe fails

```php
namespace App\Probe;

use Arty\ProbeBundle\Attribute\Probe;
use Arty\ProbeBundle\Model\ProbeInterface;

#[Probe(name: 'database_connectivity', description: 'Checks if the database is reachable')]
class DatabaseProbe implements ProbeInterface
{
    public function check(): int
    {
        // Your logic here
        // Return 0 for success, 1 for warning, 2 for failure (by default you can use Probe constants)
        return Probe::SUCCESS;
    }
}
```

## Return Value Strategies

There are two main strategies for returning values from your probes:

### Strategy 1: Using Default Thresholds

By default, the bundle uses the following status mapping based on the value returned by `check()`:

- **0**: `ProbeStatus::SUCCESS`
- **1**: `ProbeStatus::WARNING`
- **2** or more: `ProbeStatus::FAILED`

You can use the constants provided in the `Probe` attribute for clarity:

```php
public function check(): int
{
    if ($this->isHealthy()) {
        return Probe::SUCCESS; // 0
    }

    if ($this->hasMinorIssues()) {
        return Probe::WARNING; // 1
    }

    return Probe::FAILURE; // 2
}
```

### Strategy 2: Custom Thresholds

You can customize the thresholds in the `#[Probe]` attribute. This is particularly useful when the returned value represents a measurable metric, such as the number of corrupted records in a database.

```php
#[Probe(
    name: 'data_integrity',
    successThreshold: 0,
    warningThreshold: 10,
    failureThreshold: 50,
    description: 'Monitors the number of corrupted records',
    notify: true,
)]
class DataIntegrityProbe implements ProbeInterface
{
    public function check(): int
    {
        $corruptedCount = $this->repository->countCorruptedData();

        // The bundle will evaluate the status based on the thresholds:
        // < 10 => SUCCESS
        // >= 10 and < 50 => WARNING
        // >= 50 => FAILED
        return $corruptedCount;
    }
}
```

## Running Probes

You can run all registered probes using the provided console command:

```bash
php bin/console arty:probe:run
```

This will execute each probe, store the result in the database, and send an alert if a probe fails (and it wasn't already failing).

## Using the ProbeManager

The `ProbeManagerInterface` provides a set of methods to interact with the probe status history. You can inject this interface into your services to retrieve or manage probe results.

### Key Methods

- `findLastByProbeName(string $name)`: Returns the most recent status history for a specific probe.
- `findAllLastStatuses()`: Returns the latest status for every registered probe.
- `findLast5ByProbeName(string $name)`: Returns the 5 most recent status history records for a specific probe.
- `create(...)`: Creates a new `ProbeStatusHistory` instance.
- `save(ProbeStatusHistory $history)`: Persists a status history record to the database.
- `delete(ProbeStatusHistory $history)`: Removes a status history record.

### Example: Displaying Probe Status in a Controller

```php
namespace App\Controller;

use Arty\ProbeBundle\Model\ProbeManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/probes', name: 'admin_probes')]
    public function index(ProbeManagerInterface $probeManager): Response
    {
        $lastStatuses = $probeManager->findAllLastStatuses();

        return $this->render('admin/probes.html.twig', [
            'statuses' => $lastStatuses,
        ]);
    }
}
```
