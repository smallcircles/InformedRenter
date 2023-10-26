<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace org\wplake\acf_views\vendors\Twig\TokenParser;

use org\wplake\acf_views\vendors\Twig\Node\Expression\AssignNameExpression;
use org\wplake\acf_views\vendors\Twig\Node\ImportNode;
use org\wplake\acf_views\vendors\Twig\Node\Node;
use org\wplake\acf_views\vendors\Twig\Token;
/**
 * Imports macros.
 *
 *   {% from 'forms.html' import forms %}
 *
 * @internal
 */
final class FromTokenParser extends AbstractTokenParser
{
    public function parse(Token $token) : Node
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::NAME_TYPE */
            5,
            'import'
        );
        $targets = [];
        do {
            $name = $stream->expect(
                /* Token::NAME_TYPE */
                5
            )->getValue();
            $alias = $name;
            if ($stream->nextIf('as')) {
                $alias = $stream->expect(
                    /* Token::NAME_TYPE */
                    5
                )->getValue();
            }
            $targets[$name] = $alias;
            if (!$stream->nextIf(
                /* Token::PUNCTUATION_TYPE */
                9,
                ','
            )) {
                break;
            }
        } while (\true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $var = new AssignNameExpression($this->parser->getVarName(), $token->getLine());
        $node = new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
        foreach ($targets as $name => $alias) {
            $this->parser->addImportedSymbol('function', $alias, 'macro_' . $name, $var);
        }
        return $node;
    }
    public function getTag() : string
    {
        return 'from';
    }
}
