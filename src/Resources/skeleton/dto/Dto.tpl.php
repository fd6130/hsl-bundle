<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Fd\HslBundle\DtoRequestInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class <?= $class_name ?> implements DtoRequestInterface
{
<?php foreach($fieldArray as $field): ?>
    public $<?= $field['name'] ?>;
<?php endforeach; ?>

    public function __construct(Request $request)
    {
    <?php foreach($fieldArray as $field): ?>
    $this-><?= $field['name'] ?> = <?php if ($field['type'] === 'json'): ?>$request->get('<?= $field['name'] ?>'); <?php elseif ($field['type'] === 'form'): ?>$request->request->get('<?= $field['name'] ?>'); <?php else: ?>$request->files->get('<?= $field['name'] ?>');<?php endif; ?><?= "\n" ?>
    <?php endforeach; ?>
    
    }
}