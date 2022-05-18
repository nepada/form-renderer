<?php
declare(strict_types = 1);

namespace Latte {

    if (version_compare(\Latte\Engine::VERSION, '3.0', '<')) {

        class Extension
        {

        }

    }

}

namespace Latte\Macros {

    if (!version_compare(\Latte\Engine::VERSION, '3.0', '<')) {

        class MacroSet
        {

        }

    }

}
