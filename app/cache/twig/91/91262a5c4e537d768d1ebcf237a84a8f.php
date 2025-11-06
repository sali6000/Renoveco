<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* @Shared/layout/base.twig */
class __TwigTemplate_41ffda1912618dc009cb8f8c8938b5e9 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'head' => [$this, 'block_head'],
            'content' => [$this, 'block_content'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"fr\">

<head>
    ";
        // line 6
        yield "    <meta charset=\"UTF-8\" />
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
    <meta name=\"description\" content=\"Description of your page\">
    <meta name=\"keywords\" content=\"PHP, MVC, SEO, Tutorial\">
    <meta name=\"author\" content=\"Steven F\">
    <base href=\"";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["baseUri"] ?? null));
        yield "\">
    <link rel=\"shortcut icon\" href=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/about/logos/favicon.ico"), "html", null, true);
        yield "\">

    ";
        // line 15
        yield "    <title>";
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>

    ";
        // line 18
        yield "    <link rel=\"stylesheet\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("global.css"), "html", null, true);
        yield "\">

    ";
        // line 21
        yield "    ";
        if (array_key_exists("current_page", $context)) {
            // line 22
            yield "    <link rel=\"stylesheet\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()((($context["current_page"] ?? null) . ".css")), "html", null, true);
            yield "\">
    ";
        }
        // line 24
        yield "
    ";
        // line 26
        yield "    ";
        yield from $this->unwrap()->yieldBlock('head', $context, $blocks);
        // line 27
        yield "</head>

";
        // line 31
        yield "<body data-current-page=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("current_page", $context)) ? (Twig\Extension\CoreExtension::default(($context["current_page"] ?? null), "default")) : ("default")), "html", null, true);
        yield "\">

    ";
        // line 34
        yield "    ";
        try {
            $_v0 = $this->load("@Shared/components/header.twig", 34);
        } catch (LoaderError $e) {
            // ignore missing template
            $_v0 = null;
        }
        if ($_v0) {
            yield from $_v0->unwrap()->yield($context);
        }
        // line 35
        yield "
    ";
        // line 37
        yield "    <main class=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["current_page"] ?? null), "html", null, true);
        yield "\">
        <h1 class=\"sr-only\">Présentation de la page ";
        // line 38
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["current_page"] ?? null), "html", null, true);
        yield "</h1>
        ";
        // line 39
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 40
        yield "    </main>

    ";
        // line 43
        yield "    ";
        try {
            $_v1 = $this->load("@Shared/components/footer.twig", 43);
        } catch (LoaderError $e) {
            // ignore missing template
            $_v1 = null;
        }
        if ($_v1) {
            yield from $_v1->unwrap()->yield($context);
        }
        // line 44
        yield "
    ";
        // line 46
        yield "    ";
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 50
        yield "</body>
</html>";
        yield from [];
    }

    // line 15
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["entrepriseTitle"] ?? null), "html", null, true);
        yield from [];
    }

    // line 26
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_head(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 39
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 46
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 47
        yield "    <script src=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("runtime.js"), "html", null, true);
        yield "\"></script>
    <script src=\"";
        // line 48
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("app.js"), "html", null, true);
        yield "\" defer></script>
    ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@Shared/layout/base.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  200 => 48,  195 => 47,  188 => 46,  178 => 39,  168 => 26,  157 => 15,  151 => 50,  148 => 46,  145 => 44,  134 => 43,  130 => 40,  128 => 39,  124 => 38,  119 => 37,  116 => 35,  105 => 34,  99 => 31,  95 => 27,  92 => 26,  89 => 24,  83 => 22,  80 => 21,  74 => 18,  68 => 15,  63 => 12,  59 => 11,  52 => 6,  46 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@Shared/layout/base.twig", "/var/www/html/app/modules/Shared/Views/layout/base.twig");
    }
}
