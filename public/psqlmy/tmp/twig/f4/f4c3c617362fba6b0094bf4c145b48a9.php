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

/* database/routines/parameter_row.twig */
class __TwigTemplate_c2bdc83cf8415cbd4a342fa106765e24 extends Template
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
        echo "<tr>
  <td class=\"dragHandle\">
    <span class=\"ui-icon ui-icon-arrowthick-2-n-s\"></span>
  </td>
  <td class=\"routine_direction_cell";
        // line 5
        echo twig_escape_filter($this->env, ($context["class"] ?? null), "html", null, true);
        echo "\">
    <select name=\"item_param_dir[";
        // line 6
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "]\">
      ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["param_directions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["value"]) {
            // line 8
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "\"";
            echo (((($context["item_param_dir"] ?? null) == $context["value"])) ? (" selected") : (""));
            echo ">";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "</option>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        echo "    </select>
  </td>
  <td>
    <input name=\"item_param_name[";
        // line 13
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "]\" type=\"text\" value=\"";
        echo ($context["item_param_name"] ?? null);
        echo "\">
  </td>
  <td>
    <select name=\"item_param_type[";
        // line 16
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "]\">
      ";
        // line 17
        echo ($context["supported_datatypes"] ?? null);
        echo "
    </select>
  </td>
  <td>
    <input id=\"item_param_length_";
        // line 21
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "\" name=\"item_param_length[";
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "]\" type=\"text\" value=\"";
        echo ($context["item_param_length"] ?? null);
        echo "\">
    <div class=\"enum_hint\">
      <a href=\"#\" class=\"open_enum_editor\">
        ";
        // line 24
        echo PhpMyAdmin\Html\Generator::getImage("b_edit", "", ["title" => _gettext("ENUM/SET editor")]);
        echo "
      </a>
    </div>
  </td>
  <td class=\"hide no_len\">---</td>
  <td class=\"routine_param_opts_text\">
    <select lang=\"en\" dir=\"ltr\" name=\"item_param_opts_text[";
        // line 30
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "]\">
      <option value=\"\">";
echo _gettext("Charset");
        // line 31
        echo "</option>
      <option value=\"\"></option>
      ";
        // line 33
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["charsets"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["charset"]) {
            // line 34
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "name", [], "any", false, false, false, 34), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "description", [], "any", false, false, false, 34), "html", null, true);
            echo "\"";
            echo ((twig_get_attribute($this->env, $this->source, $context["charset"], "is_selected", [], "any", false, false, false, 34)) ? (" selected") : (""));
            echo ">";
            // line 35
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "name", [], "any", false, false, false, 35), "html", null, true);
            // line 36
            echo "</option>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['charset'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "    </select>
  </td>
  <td class=\"hide no_opts\">---</td>
  <td class=\"routine_param_opts_num\">
    <select name=\"item_param_opts_num[";
        // line 42
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "]\">
      <option value=\"\"></option>
      ";
        // line 44
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["param_opts_num"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["value"]) {
            // line 45
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "\"";
            echo (((($context["item_param_opts_num"] ?? null) == $context["value"])) ? (" selected") : (""));
            echo ">";
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "</option>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 47
        echo "    </select>
  </td>
  <td class=\"routine_param_remove";
        // line 49
        echo twig_escape_filter($this->env, ($context["drop_class"] ?? null), "html", null, true);
        echo "\">
    <a href=\"#\" class=\"routine_param_remove_anchor\">
      ";
        // line 51
        echo PhpMyAdmin\Html\Generator::getIcon("b_drop", _gettext("Drop"));
        echo "
    </a>
  </td>
</tr>
";
    }

    public function getTemplateName()
    {
        return "database/routines/parameter_row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  178 => 51,  173 => 49,  169 => 47,  156 => 45,  152 => 44,  147 => 42,  141 => 38,  134 => 36,  132 => 35,  124 => 34,  120 => 33,  116 => 31,  111 => 30,  102 => 24,  92 => 21,  85 => 17,  81 => 16,  73 => 13,  68 => 10,  55 => 8,  51 => 7,  47 => 6,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/routines/parameter_row.twig", "/var/www/bbc/public/psqlmy/templates/database/routines/parameter_row.twig");
    }
}
