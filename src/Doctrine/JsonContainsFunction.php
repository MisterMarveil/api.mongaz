<?php
namespace App\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonContainsFunction extends FunctionNode
{
    public $jsonField = null;
    public $value = null;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->jsonField = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->value = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'JSON_CONTAINS(%s, %s)',
            $this->jsonField->dispatch($sqlWalker),
            $this->value->dispatch($sqlWalker)
        );
    }
}
