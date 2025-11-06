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

/* @Product/list.twig */
class __TwigTemplate_5acd07d8594899cd033a31287ac794d1 extends Template
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
        yield "Liste des produits - ";
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
        yield "<div class=\"shop-layout\">
  ";
        // line 8
        yield "  <aside class=\"filters\">
    <h3>Filtres:</h3>
    <hr><br>
    <!-- Catégories -->
    <div class=\"filter-group\">
      <h4><b>Catégories:</b></h4>
      <ul>
        <li><label><input type=\"checkbox\" name=\"category\" value=\"Portes coulissantes\"> Portes coulissantes</label></li>
        <li><label><input type=\"checkbox\" name=\"category\" value=\"Chassis de fenêtres\"> Chassis de fenêtres</label></li>
        <li><label><input type=\"checkbox\" name=\"category\" value=\"Devantures en verre\"> Devantures en verre</label></li>
      </ul>
    </div>
    <br>
    <!-- Marques -->
    <div class=\"filter-group\">
      <h4><b>Marques:</b></h4>
      <ul>
        <li><label><input type=\"checkbox\" name=\"brand\" value=\"ponzio\"> Ponzio</label></li>
        <li><label><input type=\"checkbox\" name=\"brand\" value=\"procural\"> Procural</label></li>
      </ul>
    </div>
    <br>
    <!-- Prix -->
    <div class=\"filter-group\">
      <h4><b>Prix:</b></h4>
      <ul>
        <li><label><input type=\"radio\" name=\"price\" value=\"0-100\"> 0 - 100€</label></li>
        <li><label><input type=\"radio\" name=\"price\" value=\"100-300\"> 100 - 300€</label></li>
        <li><label><input type=\"radio\" name=\"price\" value=\"300-9999\"> 300€+</label></li>
      </ul>
    </div>
  </aside>

  ";
        // line 42
        yield "  <section class=\"section__products\">
    <div class=\"info\">
      <br>
      <h2>Nos produits</h2>
      <p>
        Découvrez notre large gamme de produits en aluminium, adaptés à tous vos besoins en construction et rénovation.
        Que vous recherchiez des fenêtres, des portes, des façades ou des solutions sur mesure, nous avons ce qu'il vous faut.
        Parcourez notre catalogue pour trouver les produits qui allient qualité, durabilité et design moderne.
      </p>
    </div>
    
    ";
        // line 54
        yield "    <section class=\"section__searchbar\">
      ";
        // line 55
        if ((array_key_exists("products", $context) && is_iterable(($context["products"] ?? null)))) {
            // line 56
            yield "        <label for=\"searchInput\" class=\"search-label\">🔍 Rechercher par nom :</label>
        <input type=\"text\" id=\"searchInput\" placeholder=\"PF152WG, PE50, PE78N,...\">
        <p id=\"searchResults\" class=\"no-results\" style=\"display: none;\">Aucun produit trouvé</p>
      ";
        }
        // line 60
        yield "    </section>
    <hr>

    ";
        // line 64
        yield "    <div class=\"products\">
      ";
        // line 65
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["products"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 66
            yield "          <article class=\"products__product\"
          data-category=\"";
            // line 67
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, true, false, 67), 0, [], "array", false, true, false, 67), "name", [], "any", true, true, false, 67) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (($_v0 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, false, false, 67)) && is_array($_v0) || $_v0 instanceof ArrayAccess ? ($_v0[0] ?? null) : null), "name", [], "any", false, false, false, 67)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (($_v1 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, false, false, 67)) && is_array($_v1) || $_v1 instanceof ArrayAccess ? ($_v1[0] ?? null) : null), "name", [], "any", false, false, false, 67), "html", null, true)) : (""));
            yield "\"
          data-brand=\"ponzio\" 
          data-price=\"120\">
            <p>";
            // line 70
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, true, false, 70), 0, [], "array", false, true, false, 70), "name", [], "any", true, true, false, 70) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (($_v2 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, false, false, 70)) && is_array($_v2) || $_v2 instanceof ArrayAccess ? ($_v2[0] ?? null) : null), "name", [], "any", false, false, false, 70)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (($_v3 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, false, false, 70)) && is_array($_v3) || $_v3 instanceof ArrayAccess ? ($_v3[0] ?? null) : null), "name", [], "any", false, false, false, 70), "html", null, true)) : (""));
            yield "</p>
            <figure>
        <a href=\"product/detail/";
            // line 72
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["product"], "slug", [], "any", false, false, false, 72), "html", null, true);
            yield "\">
              <img 
              class=\"preview lazy-content reveal big-slide-left\"
              data-src=\"/uploads/img/products/medium/";
            // line 75
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["product"], "images", [], "any", false, true, false, 75), 0, [], "array", false, true, false, 75), "path", [], "any", true, true, false, 75) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, (($_v4 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "images", [], "any", false, false, false, 75)) && is_array($_v4) || $_v4 instanceof ArrayAccess ? ($_v4[0] ?? null) : null), "path", [], "any", false, false, false, 75)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (($_v5 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "images", [], "any", false, false, false, 75)) && is_array($_v5) || $_v5 instanceof ArrayAccess ? ($_v5[0] ?? null) : null), "path", [], "any", false, false, false, 75), "html", null, true)) : ("placeholder.jpg"));
            yield "\"
              alt=\"";
            // line 76
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, false, 76), "html", null, true);
            yield " - ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (($_v6 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, false, false, 76)) && is_array($_v6) || $_v6 instanceof ArrayAccess ? ($_v6[0] ?? null) : null), "name", [], "any", false, false, false, 76), "html", null, true);
            yield "\">
                ";
            // line 77
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "supplier_name", [], "any", false, false, false, 77)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 78
                yield "                  <p class=\"note\"><b>Note :</b> Produit fourni par <strong>PROCURAL</strong>, garantissant une qualité et une fiabilité exceptionnelles.</p>
                ";
            }
            // line 80
            yield "        </a>
            </figure>
            <br>
            <h3 class=\"product__description__title\">Fiche technique:</h3>
            <dl>
              <dt>Nom :</dt>
              <dd>";
            // line 86
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["product"], "name", [], "any", false, false, false, 86), "html", null, true);
            yield "</dd>
              <dt>Type :</dt>
              <dd>";
            // line 88
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (($_v7 = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "categories", [], "any", false, false, false, 88)) && is_array($_v7) || $_v7 instanceof ArrayAccess ? ($_v7[0] ?? null) : null), "name", [], "any", false, false, false, 88), "html", null, true);
            yield "</dd>
              <dt>Provenance :</dt>
              <dd>";
            // line 90
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["product"], "isProcural", [], "any", false, false, false, 90)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("PROCURAL") : ("PONZIO"));
            yield "</dd>
            </dl>
          </article>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['product'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 94
        yield "    </div>
  </section>
</div>

<script type=\"application/ld+json\">";
        // line 98
        yield ($context["jsonLd"] ?? null);
        yield "</script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@Product/list.twig";
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
        return array (  215 => 98,  209 => 94,  199 => 90,  194 => 88,  189 => 86,  181 => 80,  177 => 78,  175 => 77,  169 => 76,  165 => 75,  159 => 72,  154 => 70,  148 => 67,  145 => 66,  141 => 65,  138 => 64,  133 => 60,  127 => 56,  125 => 55,  122 => 54,  109 => 42,  74 => 8,  71 => 6,  64 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@Product/list.twig", "/var/www/html/app/modules/Product/Views/list.twig");
    }
}
