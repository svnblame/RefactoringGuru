<?php 

namespace RefactoringGuru\Creational\AbstractFactory\RealWorld;

/**
 * The Abstract Factory interface declares creation methods for each distinct
 * product type.
 */
interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;
    public function createPageTemplate(): PageTemplate;
    public function getRenderer(): TemplateRenderer;
}

/**
 * Each Concrete Factory corresponds to a specific variant (or family) of
 * products.
 * 
 * This Concrete Factory creates Twig templates.
 */
class TwigTemplateFactory implements TemplateFactory 
{
    public function createTitleTemplate(): TitleTemplate 
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate 
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer 
    {
        return new TwigRenderer();
    }
}

/**
 * This Concrete Factory creates PHPTemplate templates.
 */
class PHPTemplateFactory implements TemplateFactory 
{
    public function createTitleTemplate(): TitleTemplate 
    {
        return new PHPTemplateTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate 
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer 
    {
        return new PHPTemplateRenderer();
    }
}

/**
 * Each distinct product type should have a separate interface. All variants of
 * the product must follow the same interface.
 * 
 * For instance, this Abstract Product interface describes the behavior of page
 * title templates.
 */
interface TitleTemplate 
{
    public function getTemplateString(): string;
}

/**
 * This Concrete Product provides Twig page title templates.
 */
class TwigTitleTemplate implements TitleTemplate 
{
    public function getTemplateString(): string 
    {
        return "<h1>{{ title }}</h1>";
    }
}

/**
 * This Concrete Product provides PHPTemplate page title templates.
 */
class PHPTemplateTitleTemplate implements TitleTemplate 
{
    public function getTemplateString(): string 
    {
        return "<h1><?= \$title; ?></h1>";
    }
}

/**
 * This is another Abstract Product type, which describes whole page templates.
 */
interface PageTemplate 
{
    public function getTemplateString(): string;
}

/**
 * The page template uses the title sub-template, so we have to provide the way
 * to set it in the sub-template object. The abstract factory will link the page
 * template with a title template of the same variant.
 */
