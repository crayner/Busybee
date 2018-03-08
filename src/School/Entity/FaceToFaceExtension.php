<?php
namespace App\School\Entity;

use App\Entity\Activity;
use Symfony\Component\Yaml\Yaml;

class FaceToFaceExtension extends Activity
{
    public function getTabs(): array
    {
        return Yaml::parse("
class_details:
    label: class.details.tab
    include: School/class_details.html.twig
    message: classDetailsMessage
    translation: School
class_students:
    label: class.students.tab
    include: School/class_students.html.twig
    message: classStudentsMessage
    translation: School
");
    }
}