<?php
declare(strict_types = 1);

namespace Nepada\FormRenderer;

if (false) {
    /** @deprecated use Bootstrap3RendererFactory */
    interface IBootstrap3RendererFactory extends Bootstrap3RendererFactory
    {

    }
} elseif (! interface_exists(IBootstrap3RendererFactory::class)) {
    class_alias(Bootstrap3RendererFactory::class, IBootstrap3RendererFactory::class);
}


if (false) {
    /** @deprecated use Bootstrap4RendererFactory */
    interface IBootstrap4RendererFactory extends Bootstrap4RendererFactory
    {

    }
} elseif (! interface_exists(IBootstrap4RendererFactory::class)) {
    class_alias(Bootstrap4RendererFactory::class, IBootstrap4RendererFactory::class);
}


if (false) {
    /** @deprecated use TemplateRendererFactory */
    interface ITemplateRendererFactory extends TemplateRendererFactory
    {

    }
} elseif (! interface_exists(ITemplateRendererFactory::class)) {
    class_alias(TemplateRendererFactory::class, ITemplateRendererFactory::class);
}
