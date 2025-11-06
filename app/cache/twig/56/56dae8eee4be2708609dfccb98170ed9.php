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

/* @Shared/components/footer.twig */
class __TwigTemplate_d8a7dc6928dc6adcc25779558dee6a20 extends Template
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
        yield "<footer>
  <div class=\"footerbackground\">
    <br />
    <br />
    <br />

    <div class=\"footerstructureleft\">
      <br>
      <div class=\"logoCenter\">
        <figure>
          <a href=\"#\">
            <img class=\"logo\" src=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/layouts/logos/facebook.png"), "html", null, true);
        yield "\" alt=\"facebook-logo\">
          </a>
          <figcaption>";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_entreprise_name"] ?? null), "html", null, true);
        yield "Face</figcaption>
        </figure>
        <figure>
          <a href=\"#\">
            <img class=\"logo\" src=\"";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/layouts/logos/instagram.png"), "html", null, true);
        yield "\" alt=\"instagram-logo\">
          </a>
          <figcaption>";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_entreprise_name"] ?? null), "html", null, true);
        yield "Insta</figcaption>
        </figure>
      </div>
    </div>

    <div class=\"footerstructureright\">
      <h4>Contact:</h4>
      <h5>";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_entreprise_title"] ?? null), "html", null, true);
        yield " ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_entreprise_type"] ?? null), "html", null, true);
        yield "</h5>
      <h5>Téléphone: 064/67.32.00</h5>
      <h5>Email: ";
        // line 29
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_entreprise_mail"] ?? null), "html", null, true);
        yield "</h5>
      <h5>TVA : BE0664544624</h5>

      <a href=\"cgu\">Lire les conditions générales d'utilisation du site (CGU)</a>
    </div>

    <div style=\"clear:both;\"></div>
    <br />
    <div class=\"presentation\">
      <hr>
    </div>

    <p style=\"color:darkgrey;text-align:center;\">
      &copy; 2024 ";
        // line 42
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_entreprise_name"] ?? null), "html", null, true);
        yield ". All rights reserved.
    </p>
    <br>
  </div>
</footer>
</body>

</html>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@Shared/components/footer.twig";
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
        return array (  105 => 42,  89 => 29,  82 => 27,  72 => 20,  67 => 18,  60 => 14,  55 => 12,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@Shared/components/footer.twig", "/var/www/html/app/modules/Shared/Views/components/footer.twig");
    }
}
