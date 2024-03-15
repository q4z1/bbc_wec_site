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

/* database/routines/editor_form.twig */
class __TwigTemplate_095b1c783d9919782ed3ccf062ccd8f8 extends Template
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
        echo "<form class=\"rte_form";
        echo (( !($context["is_ajax"] ?? null)) ? (" disableAjax") : (""));
        echo "\" action=\"";
        echo PhpMyAdmin\Url::getFromRoute("/database/routines");
        echo "\" method=\"post\">
  <input name=\"";
        // line 2
        echo ((($context["is_edit_mode"] ?? null)) ? ("edit_item") : ("add_item"));
        echo "\" type=\"hidden\" value=\"1\">
  ";
        // line 3
        if (($context["is_edit_mode"] ?? null)) {
            // line 4
            echo "    <input name=\"item_original_name\" type=\"hidden\" value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_original_name", [], "any", false, false, false, 4), "html", null, true);
            echo "\">
    <input name=\"item_original_type\" type=\"hidden\" value=\"";
            // line 5
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_original_type", [], "any", false, false, false, 5), "html", null, true);
            echo "\">
  ";
        }
        // line 7
        echo "  ";
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null));
        echo "

  <div class=\"card\">
    <div class=\"card-header\">
      ";
echo _gettext("Details");
        // line 12
        echo "      ";
        if ( !($context["is_edit_mode"] ?? null)) {
            // line 13
            echo "        ";
            echo PhpMyAdmin\Html\MySQLDocumentation::show("CREATE_PROCEDURE");
            echo "
      ";
        }
        // line 15
        echo "    </div>

    <div class=\"card-body\">
      <table class=\"rte_table table table-borderless table-sm\">
        <tr>
          <td>";
echo _gettext("Routine name");
        // line 20
        echo "</td>
          <td>
            <input type=\"text\" name=\"item_name\" maxlength=\"64\" value=\"";
        // line 22
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_name", [], "any", false, false, false, 22), "html", null, true);
        echo "\">
          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("Type");
        // line 26
        echo "</td>
          <td>
            ";
        // line 28
        if (($context["is_ajax"] ?? null)) {
            // line 29
            echo "              <select name=\"item_type\">
                <option value=\"PROCEDURE\"";
            // line 30
            echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 30) == "PROCEDURE")) ? (" selected") : (""));
            echo ">PROCEDURE</option>
                <option value=\"FUNCTION\"";
            // line 31
            echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 31) == "FUNCTION")) ? (" selected") : (""));
            echo ">FUNCTION</option>
              </select>
            ";
        } else {
            // line 34
            echo "              <input name=\"item_type\" type=\"hidden\" value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 34), "html", null, true);
            echo "\">
              <div class=\"fw-bold text-center w-50\">
                ";
            // line 36
            echo twig_escape_filter($this->env, (($__internal_compile_0 = ($context["routine"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["item_type"] ?? null) : null), "html", null, true);
            echo "
              </div>
              <input type=\"submit\" class=\"btn btn-secondary\" name=\"routine_changetype\" value=\"";
            // line 38
            echo twig_escape_filter($this->env, twig_sprintf(_gettext("Change to %s"), twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type_toggle", [], "any", false, false, false, 38)), "html", null, true);
            echo "\">
            ";
        }
        // line 40
        echo "          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("Parameters");
        // line 43
        echo "</td>
          <td>
            <table class=\"routine_params_table table table-borderless table-sm\">
              <thead>
                <tr>
                  <td></td>
                  <th class=\"routine_direction_cell";
        // line 49
        echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 49) == "FUNCTION")) ? (" hide") : (""));
        echo "\">";
echo _gettext("Direction");
        echo "</th>
                  <th>";
echo _gettext("Name");
        // line 50
        echo "</th>
                  <th>";
echo _gettext("Type");
        // line 51
        echo "</th>
                  <th>";
echo _gettext("Length/Values");
        // line 52
        echo "</th>
                  <th colspan=\"2\">";
echo _gettext("Options");
        // line 53
        echo "</th>
                  <th class=\"routine_param_remove hide\"></th>
                </tr>
              </thead>
              <tbody>
                ";
        // line 58
        echo ($context["parameter_rows"] ?? null);
        echo "
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            ";
        // line 66
        if (($context["is_ajax"] ?? null)) {
            // line 67
            echo "              <button type=\"button\" class=\"btn btn-primary\" id=\"addRoutineParameterButton\">";
echo _gettext("Add parameter");
            echo "</button>
            ";
        } else {
            // line 69
            echo "              <input type=\"submit\" class=\"btn btn-primary\" name=\"routine_addparameter\" value=\"";
echo _gettext("Add parameter");
            echo "\">
              <input type=\"submit\" class=\"btn btn-secondary\"  name=\"routine_removeparameter\" value=\"";
echo _gettext("Remove last parameter");
            // line 70
            echo "\"";
            echo (( !twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_num_params", [], "any", false, false, false, 70)) ? (" disabled") : (""));
            echo ">
            ";
        }
        // line 72
        echo "          </td>
        </tr>
        <tr class=\"routine_return_row";
        // line 74
        echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 74) == "PROCEDURE")) ? (" hide") : (""));
        echo "\">
          <td>";
echo _gettext("Return type");
        // line 75
        echo "</td>
          <td>
            <select name=\"item_returntype\">
              ";
        // line 78
        echo PhpMyAdmin\Util::getSupportedDatatypes(true, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_returntype", [], "any", false, false, false, 78));
        echo "
            </select>
          </td>
        </tr>
        <tr class=\"routine_return_row";
        // line 82
        echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 82) == "PROCEDURE")) ? (" hide") : (""));
        echo "\">
          <td>";
echo _gettext("Return length/values");
        // line 83
        echo "</td>
          <td>
            <input type=\"text\" name=\"item_returnlength\" value=\"";
        // line 85
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_returnlength", [], "any", false, false, false, 85), "html", null, true);
        echo "\">
          </td>
          <td class=\"hide no_len\">---</td>
        </tr>
        <tr class=\"routine_return_row";
        // line 89
        echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_type", [], "any", false, false, false, 89) == "PROCEDURE")) ? (" hide") : (""));
        echo "\">
          <td>";
echo _gettext("Return options");
        // line 90
        echo "</td>
          <td>
            <div>
              <select lang=\"en\" dir=\"ltr\" name=\"item_returnopts_text\">
                <option value=\"\">";
echo _gettext("Charset");
        // line 94
        echo "</option>
                <option value=\"\"></option>
                ";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["charsets"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["charset"]) {
            // line 97
            echo "                  <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "getName", [], "method", false, false, false, 97), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "getDescription", [], "method", false, false, false, 97), "html", null, true);
            echo "\"";
            echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_returnopts_text", [], "any", false, false, false, 97) == twig_get_attribute($this->env, $this->source, $context["charset"], "getName", [], "method", false, false, false, 97))) ? (" selected") : (""));
            echo ">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "getName", [], "method", false, false, false, 97), "html", null, true);
            echo "</option>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['charset'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 99
        echo "              </select>
            </div>
            <div>
              <select name=\"item_returnopts_num\">
                <option value=\"\"></option>
                ";
        // line 104
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["numeric_options"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["numeric_option"]) {
            // line 105
            echo "                  <option value=\"";
            echo twig_escape_filter($this->env, $context["numeric_option"], "html", null, true);
            echo "\"";
            echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_returnopts_num", [], "any", false, false, false, 105) == $context["numeric_option"])) ? (" selected") : (""));
            echo ">";
            echo twig_escape_filter($this->env, $context["numeric_option"], "html", null, true);
            echo "</option>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['numeric_option'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 107
        echo "              </select>
            </div>
            <div class=\"hide no_opts\">---</div>
          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("Definition");
        // line 113
        echo "</td>
          <td>
            <textarea name=\"item_definition\" rows=\"15\" cols=\"40\">";
        // line 115
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_definition", [], "any", false, false, false, 115), "html", null, true);
        echo "</textarea>
          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("Is deterministic");
        // line 119
        echo "</td>
          <td>
            <input type=\"checkbox\" name=\"item_isdeterministic\"";
        // line 121
        echo twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_isdeterministic", [], "any", false, false, false, 121);
        echo ">
          </td>
        </tr>

        ";
        // line 125
        if (($context["is_edit_mode"] ?? null)) {
            // line 126
            echo "          <tr>
            <td>
              ";
echo _gettext("Adjust privileges");
            // line 129
            echo "              ";
            echo PhpMyAdmin\Html\MySQLDocumentation::showDocumentation("faq", "faq6-39");
            echo "
            </td>
            <td>
              ";
            // line 132
            if (($context["has_privileges"] ?? null)) {
                // line 133
                echo "                <input type=\"checkbox\" name=\"item_adjust_privileges\" value=\"1\" checked>
              ";
            } else {
                // line 135
                echo "                <input type=\"checkbox\" name=\"item_adjust_privileges\" value=\"1\" title=\"";
echo _gettext("You do not have sufficient privileges to perform this operation; Please refer to the documentation for more details.");
                echo "\" disabled>
              ";
            }
            // line 137
            echo "            </td>
          </tr>
        ";
        }
        // line 140
        echo "
        <tr>
          <td>";
echo _gettext("Definer");
        // line 142
        echo "</td>
          <td>
            <input type=\"text\" name=\"item_definer\" value=\"";
        // line 144
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_definer", [], "any", false, false, false, 144), "html", null, true);
        echo "\">
          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("Security type");
        // line 148
        echo "</td>
          <td>
            <select name=\"item_securitytype\">
              <option value=\"DEFINER\"";
        // line 151
        echo twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_securitytype_definer", [], "any", false, false, false, 151);
        echo ">DEFINER</option>
              <option value=\"INVOKER\"";
        // line 152
        echo twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_securitytype_invoker", [], "any", false, false, false, 152);
        echo ">INVOKER</option>
            </select>
          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("SQL data access");
        // line 157
        echo "</td>
          <td>
            <select name=\"item_sqldataaccess\">
              ";
        // line 160
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["sql_data_access"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["value"]) {
            // line 161
            echo "                <option value=\"";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "\"";
            echo (((twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_sqldataaccess", [], "any", false, false, false, 161) == $context["value"])) ? (" selected") : (""));
            echo ">";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "</option>
              ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 163
        echo "            </select>
          </td>
        </tr>
        <tr>
          <td>";
echo _gettext("Comment");
        // line 167
        echo "</td>
          <td>
            <input type=\"text\" name=\"item_comment\" maxlength=\"64\" value=\"";
        // line 169
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["routine"] ?? null), "item_comment", [], "any", false, false, false, 169), "html", null, true);
        echo "\">
          </td>
        </tr>
      </table>
    </div>

    ";
        // line 175
        if (($context["is_ajax"] ?? null)) {
            // line 176
            echo "      <input type=\"hidden\" name=\"";
            echo ((($context["is_edit_mode"] ?? null)) ? ("editor_process_edit") : ("editor_process_add"));
            echo "\" value=\"true\">
      <input type=\"hidden\" name=\"ajax_request\" value=\"true\">
    ";
        } else {
            // line 179
            echo "      <div class=\"card-footer\">
        <input class=\"btn btn-primary\" type=\"submit\" name=\"";
            // line 180
            echo ((($context["is_edit_mode"] ?? null)) ? ("editor_process_edit") : ("editor_process_add"));
            echo "\" value=\"";
echo _gettext("Go");
            echo "\">
      </div>
    ";
        }
        // line 183
        echo "  </div>
</form>
";
    }

    public function getTemplateName()
    {
        return "database/routines/editor_form.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  460 => 183,  452 => 180,  449 => 179,  442 => 176,  440 => 175,  431 => 169,  427 => 167,  420 => 163,  407 => 161,  403 => 160,  398 => 157,  389 => 152,  385 => 151,  380 => 148,  372 => 144,  368 => 142,  363 => 140,  358 => 137,  352 => 135,  348 => 133,  346 => 132,  339 => 129,  334 => 126,  332 => 125,  325 => 121,  321 => 119,  313 => 115,  309 => 113,  300 => 107,  287 => 105,  283 => 104,  276 => 99,  261 => 97,  257 => 96,  253 => 94,  246 => 90,  241 => 89,  234 => 85,  230 => 83,  225 => 82,  218 => 78,  213 => 75,  208 => 74,  204 => 72,  198 => 70,  192 => 69,  186 => 67,  184 => 66,  173 => 58,  166 => 53,  162 => 52,  158 => 51,  154 => 50,  147 => 49,  139 => 43,  133 => 40,  128 => 38,  123 => 36,  117 => 34,  111 => 31,  107 => 30,  104 => 29,  102 => 28,  98 => 26,  90 => 22,  86 => 20,  78 => 15,  72 => 13,  69 => 12,  60 => 7,  55 => 5,  50 => 4,  48 => 3,  44 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/routines/editor_form.twig", "/var/www/bbc/public/psqlmy/templates/database/routines/editor_form.twig");
    }
}
