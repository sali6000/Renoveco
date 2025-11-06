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

/* @Shared/components/header.twig */
class __TwigTemplate_0d0c4d78ebdc1ef76f747fd99e4ad655 extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<header>
  <div class=\"navbar\">
    <div class=\"navbar__home\">
      <a href=\"/\" aria-label=\"Accueil\">
        <img src=\"";
        // line 5
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/about/logos/alupon.png"), "html", null, true);
        yield "\" alt=\"Alupon - Accueil\">
      </a>
    </div>

    <nav class=\"navbar__menu\" aria-label=\"Navigation principale\">
      <ul>
        <li><a href=\"product/list\">Produits</a></li>
        <li><a href=\"about\">Société</a></li>
        <li><a href=\"contact\">Contact</a></li>
        ";
        // line 14
        if ((($tmp = ($context["userSession"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 15
            yield "        <li><a href=\"auth/logout\">Profil ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["userSession"] ?? null), "email", [], "any", false, false, false, 15), "html", null, true);
            yield "</a></li>
        <li><a href=\"auth/logout\">Déconnection</a></li>
        ";
        } else {
            // line 18
            yield "        <li><a href=\"auth/login\">Connection</a></li>
        ";
        }
        // line 20
        yield "      </ul>
    </nav>

    <div class=\"navbar__contact\">
      <img src=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/layouts/emoticons/phone.png"), "html", null, true);
        yield "\" alt=\"Téléphone\">
      <span>+3264/67.32.00 <b><br>Horaire:</b> Lun - Ven 8h - 16h</span>
    </div>

  </div>
</header>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@Shared/components/header.twig";
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
        return array (  79 => 24,  73 => 20,  69 => 18,  62 => 15,  60 => 14,  48 => 5,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@Shared/components/header.twig", "/var/www/html/app/modules/Shared/Views/components/header.twig");
    }
}
