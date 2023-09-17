<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/custom/solutions_portal/templates/entity-tree.html.twig */
class __TwigTemplate_840c3fb2da0eb7700ce216bf6233e9d8 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
        $macros["_self"] = $this->macros["_self"] = $this;
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 13
        echo "

<div id=\"";
        // line 15
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($this->sandbox->ensureToStringAllowed(($context["field_id"] ?? null), 15, $this->source) . "_taxonomy_tree"), "html", null, true);
        echo "\">
  <ul>
    ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["terms"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["terms_list"]) {
            // line 18
            echo "      ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["terms_list"]);
            foreach ($context['_seq'] as $context["_key"] => $context["term"]) {
                // line 19
                echo "          ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["_self"], "macro_renderTerm", [$context["term"]], 19, $context, $this->getSourceContext()));
                echo "
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['term'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['terms_list'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 22
        echo "  </ul>
</div>
";
    }

    // line 1
    public function macro_renderTerm($__term__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "term" => $__term__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start(function () { return ''; });
        try {
            // line 2
            echo "  <li>
    ";
            // line 3
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["term"] ?? null), "name", [], "any", false, false, true, 3), 3, $this->source), "html", null, true);
            echo "
    ";
            // line 4
            if (twig_get_attribute($this->env, $this->source, ($context["term"] ?? null), "children", [], "any", false, false, true, 4)) {
                // line 5
                echo "      <ul>
        ";
                // line 6
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["term"] ?? null), "children", [], "any", false, false, true, 6));
                foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                    // line 7
                    echo "            ";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["_self"], "macro_renderTerm", [$context["child"]], 7, $context, $this->getSourceContext()));
                    echo "
        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 9
                echo "      </ul>
    ";
            }
            // line 11
            echo "  </li>
";

            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    public function getTemplateName()
    {
        return "modules/custom/solutions_portal/templates/entity-tree.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 11,  117 => 9,  108 => 7,  104 => 6,  101 => 5,  99 => 4,  95 => 3,  92 => 2,  79 => 1,  73 => 22,  67 => 21,  58 => 19,  53 => 18,  49 => 17,  44 => 15,  40 => 13,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/custom/solutions_portal/templates/entity-tree.html.twig", "/var/www/web/modules/custom/solutions_portal/templates/entity-tree.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("for" => 17, "macro" => 1, "if" => 4);
        static $filters = array("escape" => 15, "raw" => 19);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['for', 'macro', 'if', 'import'],
                ['escape', 'raw'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
