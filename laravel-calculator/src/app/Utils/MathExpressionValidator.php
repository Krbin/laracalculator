<?php

namespace App\Utils;

class MathExpressionValidator
{
    public static function isValid(string $input): bool
    {
        $regex = <<<REGEX
        (?x)
        (?(DEFINE)

            (?<number>
                \d+(?:\.\d+)?
            )

            (?<constant>
                pi | π | e
            )

            (?<function_name>
                sin | cos | tan | log | ln
            )

            (?<expression>

                (?&simple_expression)

                (?: 

                    \s* (?<explicit_operator>

                        (?<operator_symbol>
                            [\^*\/+\-]
                        )

                    ) \s*

                    (?&simple_expression)


                    |

                    \s* (?<implicit_multiplier>
                        (?=
                            (?&simple_expression)
                        )
                    )

                    (?&simple_expression)

                )*

            )
            (?<simple_expression>

                (?&number)

                |

                (?&constant)

                |

                (?<function>

                    (?&function_name) \s*
                    (?:

                        \(
                            \s* (?&expression) \s*
                        \)

                        |

                        \[
                            \s* (?&expression) \s*
                        \]

                        |

                        \{
                            \s* (?&expression) \s*
                        \})

                    )

                |

                    \(
                        \s* (?&expression) \s*
                    \)

                |

                    \[
                        \s* (?&expression) \s*
                    \]

                |
                    \{
                        \s* (?&expression)\s*
                    \}
            )
        )

        ^(?&expression)$
        REGEX;

        return (bool)preg_match("/$regex/x", $input);
    }
}