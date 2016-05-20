<?php declare (strict_types = 1);

use ast\Node;
use Phan\AST\AnalysisVisitor;
use Phan\CodeBase;
use Phan\Language\Context;
use Phan\Plugin;
use Phan\Plugin\PluginImplementation;

class EvalPlugin extends PluginImplementation {
    /**
     * @param CodeBase $code_base
     * The code base in which the node exists
     *
     * @param Context $context
     * The context in which the node exits. This is
     * the context inside the given node rather than
     * the context outside of the given node
     *
     * @param Node $node
     * The php-ast Node being analyzed.
     *
     * @param Node $node
     * The parent node of the given node (if one exists).
     *
     * @return void
     */
    public function analyzeNode(
        CodeBase $code_base,
        Context $context,
        Node $node,
        Node $parent_node = null
    ) {
        (new EvalVisitor($code_base, $context, $this))(
            $node
        );

    }
}

class EvalVisitor extends AnalysisVisitor {
    /** @var Plugin */
    private $plugin;

    public function __construct(
        CodeBase $code_base,
        Context $context,
        Plugin $plugin
    ) {
        parent::__construct($code_base, $context);

        $this->plugin = $plugin;
    }

    /**
     * Default visitor that does nothing
     *
     * @param Node $node
     * A node to analyze
     *
     * @return void
     */
    public function visit(Node $node) {
    }

    /**
     * @param Node $node
     * A node to analyze
     *
     * @return void
     */
    public function visitIncludeOrEval(Node $node) {
        if ($node->flags === ast\flags\EXEC_EVAL) {
            $this->triggerIssue("eval");
        }
    }

    /**
     * @param Node $node
     * A node to analyze
     *
     * @return void
     */

    public function visitCall(Node $node) {
        if (isset($node->children['expr'])) {
            if ($node->children['expr']->kind == \ast\AST_NAME) {
                $function_name = $node->children['expr']->children['name'];
                if ($function_name === "create_function") {
                    $this->triggerIssue("create_function");
                }
            }
        }
    }

    /**
     * @return void
     */
    private function triggerIssue(string $expression) {
        $this->plugin->emitIssue(
            $this->code_base,
            $this->context,
            'PhanPluginEval',
            "`$expression` is not allowed."
        );

    }
}

return new EvalPlugin();
