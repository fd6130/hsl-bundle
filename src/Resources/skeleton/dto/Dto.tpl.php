<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Dto\DtoRequestInterface;
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
    $this-><?= $field['name'] ?> = <?php if ($field['type'] === 'json'): ?>$request->get('<?= $field['name'] ?>'); <?php else: ?>$request->request->get('<?= $field['name'] ?>'); <?php endif; ?>
    <?php endforeach; ?>
    
    }
}