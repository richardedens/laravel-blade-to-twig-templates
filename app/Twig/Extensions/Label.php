<?php

    namespace App\Twig\Extensions;

    use Twig_Extension;
    use Twig_SimpleFunction;
    use Session;
    use DB;

    class Label extends Twig_Extension
    {
        public function getName()
        {
            return 'label';
        }

        public function getFunctions()
        {
            return [
                'label' => new Twig_SimpleFunction('label', [$this, 'getLabel'], ['is_safe' => ['html']]),
            ];
        }

        

    }
