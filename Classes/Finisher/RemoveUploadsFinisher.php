<?php
declare(strict_types=1);
namespace Wwwision\Form\SecureFileUpload\Finisher;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Form\Core\Model\FinisherContext;
use Neos\Form\Core\Model\FinisherInterface;

/**
 * A Form Finisher that removes all uploaded resources
 *
 * Note: This finisher only makes sense *after* a finisher that processes the files first (e.g. EmailFinisher)
 */
final class RemoveUploadsFinisher implements FinisherInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    public function execute(FinisherContext $finisherContext): void
    {
        foreach ($finisherContext->getFormValues() as $formValue) {
            if (!$formValue instanceof PersistentResource) {
                continue;
            }
            $this->resourceManager->deleteResource($formValue);
        }
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function setOption($optionName, $optionValue): void
    {
        $this->options[$optionName] = $optionValue;
    }
}
