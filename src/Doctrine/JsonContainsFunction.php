<?php
namespace App\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class JsonContainsFunction extends FunctionNode
{    
    public $jsonField = null;
    public $value = null;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER); // (2)
        $parser->match(TokenType::T_OPEN_PARENTHESIS); // (3)
        $this->jsonField = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA); // (5)
        $this->value = $parser->ArithmeticPrimary(); // (6)
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);         
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
