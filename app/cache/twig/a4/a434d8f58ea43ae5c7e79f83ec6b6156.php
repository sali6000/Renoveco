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

/* @About/index.twig */
class __TwigTemplate_c351cc9d38e9691af5d02e62de349f6d extends Template
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
        yield "A propos - ";
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
        yield "<div class=\"cell-about\">
  <div class=\"logo-container\">
    <img class=\"logo lazy-content\" data-src=\"";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/about/logos/alupon.png"), "html", null, true);
        yield "\" alt=\"logo-author\">
  </div>
  <hr><br>

  <div class=\"image-auteur\">
    <img class=\"lazy-content\" data-src=\"";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('encore_asset')->getCallable()("img/about/entreprise.jpg"), "html", null, true);
        yield "\" alt=\"img-author\">
  </div>

  <div class=\"text-description-about\">
    <span>
      SAWY développe une œuvre graphique, inspirée du Street-Art, bariolée et fourmillante d'objets et de personnages humoristiques rappelant les univers Hip-Hop, de la BD, de l’animation et du Gaming.
      <br><br>
      Artiste prolixe, il se déploie sur tous les supports, de la toile aux briques, en passant par des objets chinés au quotidien qu'il aime customiser et assembler pour transmettre ses messages aux « folks » (peuples).
      <br><br>
      Depuis 2017, il s’aventure dans une collection d’œuvres poético-PoP à la gamme de couleurs vanillées dans un esprit très Miami Vice, avec ses intentions engagées sur notre société de consommation et de malbouffe — ce sont pour lui les débordements du capitalisme.
      <br><br>
      Pour 2019, il développe plusieurs séries qui suivent l’actualité des salaires sportifs, ses amours vintage des années 90, … Jeune artiste hip-hop belgo-marocain de 30 ans, la création de ses œuvres est principalement opérée à partir d’éléments récupérés et améliorés pour leur donner une seconde vie inespérée.
    </span>
    <br><br>

    <h1 style=\"text-align: center;\">Parcours</h1>
    <br>

    <span>
      Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
      tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
      quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
      consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
      cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
      proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </span>
    <br>

    <ul>
      <li>Texte 1</li>
      <li>Texte 2</li>
      <li>Texte 3</li>
      <li>Texte 4</li>
      <li>Texte 5</li>
      <li>Texte 6</li>
    </ul>
  </div>
  <div style=\"clear:both;\"></div>
  <br><br>
<hr />
<br><br>
  <section class=\"section__info\" aria-label=\"Localisation du siège\">
  <h2>Localisation du siège:</h2>
    <iframe width=\"50%\" src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2542.450072261545!2d4.45035572561422!3d50.41408558998268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c2260e1d80eced%3A0xaa4301377e208fe!2sStade%20du%20Pays%20de%20Charleroi!5e0!3m2!1sfr!2sbe!4v1758113735554!5m2!1sfr!2sbe\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>
  </section>


</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@About/index.twig";
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
        return array (  83 => 13,  75 => 8,  71 => 6,  64 => 5,  52 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@About/index.twig", "/var/www/html/app/modules/About/Views/index.twig");
    }
}
