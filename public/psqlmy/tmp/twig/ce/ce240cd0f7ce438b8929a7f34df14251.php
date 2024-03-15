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

/* database/search/results.twig */
class __TwigTemplate_d516e10afb0061598efc0dba7217b1bc extends Template
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
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<table class=\"table table-striped caption-top w-auto\">
    <caption class=\"tblHeaders\">
        ";
        // line 3
        echo twig_sprintf("Search results for \"<em>%s</em>\" %s:",         // line 4
($context["criteria_search_string"] ?? null),         // line 5
($context["search_type_description"] ?? null));
        // line 6
        echo "
    </caption>
    ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["rows"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 9
            echo "        <tr class=\"noclick\">
            <td>
                ";
            // line 11
            ob_start(function () { return ''; });
            // line 12
            echo "                    ";
echo _ngettext("%1\$s match in <strong>%2\$s</strong>", "%1\$s matches in <strong>%2\$s</strong>", abs(twig_get_attribute($this->env, $this->source,             // line 14
$context["row"], "result_count", [], "any", false, false, false, 14)));
            // line 17
            echo "                ";
            $context["result_message"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 18
            echo "                ";
            echo twig_sprintf(($context["result_message"] ?? null), twig_get_attribute($this->env, $this->source, $context["row"], "result_count", [], "any", false, false, false, 18), twig_get_attribute($this->env, $this->source, $context["row"], "table", [], "any", false, false, false, 18));
            echo "
            </td>
            ";
            // line 20
            if ((twig_get_attribute($this->env, $this->source, $context["row"], "result_count", [], "any", false, false, false, 20) > 0)) {
                // line 21
                echo "                ";
                $context["url_params"] = ["db" =>                 // line 22
($context["db"] ?? null), "table" => twig_get_attribute($this->env, $this->source,                 // line 23
$context["row"], "table", [], "any", false, false, false, 23), "goto" => PhpMyAdmin\Url::getFromRoute("/database/sql"), "pos" => 0, "is_js_confirmed" => 0];
                // line 28
                echo "                <td>
                    <a name=\"browse_search\"
                        class=\"ajax browse_results\"
                        href=\"";
                // line 31
                echo PhpMyAdmin\Url::getFromRoute("/sql", ($context["url_params"] ?? null));
                echo "\"
                        data-browse-sql=\"";
                // line 32
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["row"], "new_search_sqls", [], "any", false, false, false, 32), "select_columns", [], "any", false, false, false, 32), "html", null, true);
                echo "\"
                        data-table-name=\"";
                // line 33
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "table", [], "any", false, false, false, 33), "html", null, true);
                echo "\">
                        ";
echo _gettext("Browse");
                // line 35
                echo "                    </a>
                </td>
                <td>
                    <a name=\"delete_search\"
                        class=\"ajax delete_results\"
                        href=\"";
                // line 40
                echo PhpMyAdmin\Url::getFromRoute("/sql", ($context["url_params"] ?? null));
                echo "\"
                        data-delete-sql=\"";
                // line 41
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["row"], "new_search_sqls", [], "any", false, false, false, 41), "delete", [], "any", false, false, false, 41), "html", null, true);
                echo "\"
                        data-table-name=\"";
                // line 42
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["row"], "table", [], "any", false, false, false, 42), "html", null, true);
                echo "\">
                        ";
echo _gettext("Delete");
                // line 44
                echo "                    </a>
                </td>
            ";
            } else {
                // line 47
                echo "                <td></td>
                <td></td>
            ";
            }
            // line 50
            echo "        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 52
        echo "</table>

";
        // line 54
        if ((twig_length_filter($this->env, ($context["criteria_tables"] ?? null)) > 1)) {
            // line 55
            echo "    <p>
        ";
echo strtr(_ngettext("<strong>Total:</strong> <em>%count%</em> match", "<strong>Total:</strong> <em>%count%</em> matches", abs(            // line 58
($context["result_total"] ?? null))), array("%count%" => abs(($context["result_total"] ?? null)), "%count%" => abs(($context["result_total"] ?? null)), ));
            // line 61
            echo "    </p>
";
        }
    }

    public function getTemplateName()
    {
        return "database/search/results.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  145 => 61,  143 => 58,  140 => 55,  138 => 54,  134 => 52,  127 => 50,  122 => 47,  117 => 44,  112 => 42,  108 => 41,  104 => 40,  97 => 35,  92 => 33,  88 => 32,  84 => 31,  79 => 28,  77 => 23,  76 => 22,  74 => 21,  72 => 20,  66 => 18,  63 => 17,  61 => 14,  59 => 12,  57 => 11,  53 => 9,  49 => 8,  45 => 6,  43 => 5,  42 => 4,  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/search/results.twig", "/var/www/bbc/public/psqlmy/templates/database/search/results.twig");
    }
}
