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

/* @Home/index.twig */
class __TwigTemplate_97a691a1fd22ec9d12284746b010618e extends Template
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
        // line 3
        $context["current_page"] = "home";
        // line 1
        $this->parent = $this->load("@Shared/layout/base.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Accueil - ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        yield from [];
    }

    // line 7
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 8
        yield "
";
        // line 10
        yield "<section class=\"section__slides\" id=\"slides-js\" aria-label=\"Slider de présentation\">
  ";
        // line 11
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range(1, 3));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 12
            yield "  <div class=\"slides__slide\">
    <video autoplay muted loop playsinline preload=\"auto\" width=\"100%\">
      <source src=\"";
            // line 14
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()((("webm/home/video" . $context["i"]) . ".webm")), "html", null, true);
            yield "\" type=\"video/webm\" />
    </video>
    <div class=\"slides__slide__texts\"> 
      ";
            // line 17
            if (($context["i"] == 1)) {
                // line 18
                yield "        <h2 class=\"reveal-infiny slide-up\">Laissez vous guider <br>par nos experts. <br>Partouts en Belgique.</h2>
      ";
            } elseif ((            // line 19
$context["i"] == 2)) {
                // line 20
                yield "        <h2 class=\"reveal-infiny slide-up\">Un service de qualité <br><b>sans engagements.</b></h2>
      ";
            } else {
                // line 22
                yield "        <h2 class=\"reveal-infiny slide-up\">Des offres et des produits <br>défiants toutes concurrences.</h2>
      ";
            }
            // line 24
            yield "      <a href=\"message\">Contactez-nous</a>
      <br><br>
    </div>
  </div>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['i'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        yield "  <div class=\"slides__style--arrow_left slides__event--prev\"></div>
  <div class=\"slides__style--arrow_right slides__event--next\"></div>
</section>
  ";
        // line 33
        yield "
";
        // line 35
        yield "<section class=\"section__info__wrapper\" aria-label=\"Arguments de vente\">
  <h2 class=\"texte1\">Économique - Écologique - Pratique</h2>
</section>
";
        // line 39
        yield "
";
        // line 41
        yield "<section class=\"section__info__wrapper reveal big-slide-right\" aria-label=\"Catégories\">
    <h2>Découvrez nos services</h2>
    <div class=\"categories\">
    <a href=\"/devis\">
      <img src=\"";
        // line 45
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/home/image11.jpg"), "html", null, true);
        yield "\" alt=\"Établir un devis\">
      <p>Établir un devis</p>
    </a>
    <a href=\"/installation\">
      <img src=\"";
        // line 49
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/home/image12.jpg"), "html", null, true);
        yield "\" alt=\"Installation\">
      <p>Installation</p>
    </a>
    <a href=\"/catalogue\">
      <img src=\"";
        // line 53
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/home/image13.jpg"), "html", null, true);
        yield "\" alt=\"Catalogue\">
      <p>Catalogue</p>
    </a>
    </div>
</section>
";
        // line 59
        yield "
";
        // line 61
        yield "<section class=\"section__info__wrapper\" aria-label=\"Présentation de l'entreprise\">
  <h2 class=\"texte2\">L'experience Belge entre les mains des professionnels <b>depuis 20 ans</b></h2>
  <h3 class=\"texte3\">Demandez une expertise <b>GRATUITEMENT</b></h3>
</section>
";
        // line 66
        yield "
";
        // line 68
        yield "<section class=\"section__info reveal slide-down\" aria-label=\"Découverte de l'entreprise\">
  <div class=\"about\">
    <img src=\"";
        // line 70
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/home/image14.jpg"), "html", null, true);
        yield "\" alt=\"ouvriers\">
    <div class=\"description resizeText\">
      <h2>Notre savoir faire:</h2> </br>
      <h3>
         loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...loremlorem Lorem ipsum dolor sit amet consectetur adipisicing elit...
        (reste du texte ici)
      </h3>
    </div>
  </div>
</section>
  ";
        // line 81
        yield "
  ";
        // line 83
        yield "  <!--
  <section class=\"section__info\" aria-label=\"Localisation du siège\">
  <h2>Localisation du siège:</h2>
    <iframe width=\"50%\" src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2542.450072261545!2d4.45035572561422!3d50.41408558998268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c2260e1d80eced%3A0xaa4301377e208fe!2sStade%20du%20Pays%20de%20Charleroi!5e0!3m2!1sfr!2sbe!4v1758113735554!5m2!1sfr!2sbe\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>
  </section>
  -->
  ";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@Home/index.twig";
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
        return array (  195 => 83,  192 => 81,  179 => 70,  175 => 68,  172 => 66,  166 => 61,  163 => 59,  155 => 53,  148 => 49,  141 => 45,  135 => 41,  132 => 39,  127 => 35,  124 => 33,  119 => 29,  109 => 24,  105 => 22,  101 => 20,  99 => 19,  96 => 18,  94 => 17,  88 => 14,  84 => 12,  80 => 11,  77 => 10,  74 => 8,  67 => 7,  55 => 5,  50 => 1,  48 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@Home/index.twig", "/var/www/html/app/modules/Home/Views/index.twig");
    }
}
