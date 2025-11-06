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

/* @Product/detail.twig */
class __TwigTemplate_b76a00dc8f39b4e093fec3232790e62b extends Template
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

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "@Shared/layout/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("@Shared/layout/base.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Détail du produit - ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 6
        yield "<nav aria-label=\"breadcrumb\">
  <ol class=\"breadcrumb\">
    <li class=\"breadcrumb-item\"><a href=\"/product/list\">Produits</a></li>
    <li class=\"breadcrumb-item\"><a href=\"/product/list/";
        // line 9
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "category_name", [], "any", false, false, false, 9), "html", null, true);
        yield "\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "category_name", [], "any", false, false, false, 9), "html", null, true);
        yield "</a></li>
    <li class=\"breadcrumb-item active\" aria-current=\"page\">";
        // line 10
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "name", [], "any", false, false, false, 10), "html", null, true);
        yield "</li>
  </ol>
</nav>
<br>

<div class=\"thumbnails\">
  <div class=\"left-div image-description\"><br><br>
    ";
        // line 17
        if ((CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "isProcural", [], "any", false, false, false, 17) == "1")) {
            // line 18
            yield "      <img class=\"lazy-content\" data-src=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/suppliers/procural.jpg"), "html", null, true);
            yield "\" alt=\"procural\">
    ";
        }
        // line 20
        yield "    <br><br>
    <h2><b>";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "name", [], "any", false, false, false, 21), "html", null, true);
        yield "</b></h2>
    <p>
      Gamme destinée pour les maisons, restaurants reliant l’espace intérieur à l’extérieur. <br><br>
      Gammes à 3 chambres assurant une haute résistance des profilés et possibilité de grandes dimensions (L ≤ 1200 mm ou H ≤ 3500 mm, poids max vantail - 120 kg).<br><br>
      Haute isolation thermique grâce aux barrettes thermiques de 34 mm pour dormants et vantaux.<br>
      Quincaillerie spécialisée pour haute fonctionnalité.<br>
      Plage de vitrage : 22 - 60 mm.<br>
      Choix libre du seuil.<br>
      Possibilité de liaison avec autres gammes PROCURAL PE78N.
    </p>
  </div>

  <div class=\"middle-div\">
    <img id=\"mainImage\" class=\"image-container lazy-content reveal big-slide-right\" data-src=\"/uploads/img/products/large/";
        // line 34
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (($_v0 = CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "images", [], "any", false, false, false, 34)) && is_array($_v0) || $_v0 instanceof ArrayAccess ? ($_v0[0] ?? null) : null), "path", [], "any", false, false, false, 34), "html", null, true);
        yield "\" alt=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "name", [], "any", false, false, false, 34), "html", null, true);
        yield "\"><br>
    <div class=\"image-container-texte\">
      Prenez l'image en main en cliquant dessus<br>Zoomez avec la roulette<br>Parcourez les recoins de l'image en la déplaçant
    </div>
  </div>

  <div class=\"right-div\">
    <!-- PREVIEWS -->
    <div class=\"container\">
      <div class=\"parent\">
        <h2 class=\"title-button\">Autres aperçus</h2>
        <div class=\"hidden-content\">
          <div class=\"previews\">
            ";
        // line 47
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["previews"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
            // line 48
            yield "              <figure class=\"bottom-div\">
                <img class=\"thumbnail lazy-content\" data-src=\"";
            // line 49
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((($context["base_url_assets_img_materials"] ?? null) . $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "link", [], "any", false, false, false, 49))), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "name", [], "any", false, false, false, 49));
            yield "\">
              </figure>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['p'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 52
        yield "          </div>
        </div>
      </div>
    </div>

    <!-- FICHE TECHNIQUE -->
    <div class=\"container\">
      <div class=\"parent\">
        <h2 class=\"title-button\">Fiche technique</h2>
        <div class=\"hidden-content\">
          <b>Nom du produit :</b><br>
          <hr>";
        // line 63
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["model"] ?? null), "name", [], "any", false, false, false, 63), "html", null, true);
        yield "
          <br><br><br>
          <b>Type de produit :</b><br>
          <hr>Aluminium
          <br><br><br>
          <b>Fabricant :</b><br>
          <hr>Ponzio
          <br><br><br>
          <b>Profilé aluminium :</b>
          <hr>EN AW-6060 selon PN-EN 573-3 état T6 ou T66 selon PN-EN 515 Al Mg Si 0,5 F22 selon les normes DIN 1725 T1, DIN 17615 T1
          <br><br>
          <b>Joints :</b>
          <hr>Caoutchouc synthétique EPDM selon la norme DIN 7863 et ISO 3302-01, E2
          <br><br>
          <b>Dimensions max du vantail :</b>
          <hr>L 1700 x H 2200 mm, L 1300 x H 3000 mm
        </div>
      </div>
    </div>

    ";
        // line 83
        if ((($tmp = ($context["scheme_pdf_exists"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 84
            yield "      <!-- PDF -->
      <div class=\"hrbis\"></div>
      <div class=\"container\">
        <div class=\"parent\">
          <h2 class=\"title-button\">Schémas disponibles (PDF)</h2>
          <div class=\"hidden-content\">
            <a href=\"";
            // line 90
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["scheme_pdf_url"] ?? null), "html", null, true);
            yield "\" target=\"_blank\">
              <ul>
                <li>";
            // line 92
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["file"] ?? null), "name", [], "any", false, false, false, 92), "html", null, true);
            yield "</li>
              </ul>
            </a>
          </div>
        </div>
      </div>
    ";
        }
        // line 99
        yield "  </div>
</div>

";
        // line 102
        if ((($tmp = ($context["scheme_pdf_exists"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 103
            yield "  <div class=\"quality\">
    <br><br><br><br> Schéma de la pièce : <br><br><br><br>
    <iframe id=\"pdfIframe\" src=\"";
            // line 105
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["scheme_pdf_url"] ?? null), "html", null, true);
            yield "\" width=\"100%\" height=\"600px\">Ce navigateur ne supporte pas les iframes.</iframe>
    <br><br>
  </div>
";
        }
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@Product/detail.twig";
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
        return array (  227 => 105,  223 => 103,  221 => 102,  216 => 99,  206 => 92,  201 => 90,  193 => 84,  191 => 83,  168 => 63,  155 => 52,  144 => 49,  141 => 48,  137 => 47,  119 => 34,  103 => 21,  100 => 20,  94 => 18,  92 => 17,  82 => 10,  76 => 9,  71 => 6,  64 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@Product/detail.twig", "/var/www/html/app/modules/Product/Views/detail.twig");
    }
}
