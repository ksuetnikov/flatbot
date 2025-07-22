<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'blank_line_before_statement' => ['statements' => ['return']],
        // сюда другие правила по необходимости
        'align_multiline_comment' => ['comment_type' => 'phpdocs_only'],
        // Каждый элемент массива должен иметь отступ ровно один раз
        'array_indentation' => true,
        // Преобразует простое использование array_push($x, $y); в $x[] = $y;
        'array_push' => true,
        // Массивы PHP следует объявлять с использованием короткого синтаксиса [1,2];, а не array(1,2);
        'array_syntax' => ['syntax' => 'short'],
        // ??= По возможности используйте оператор присваивания: $foo ??= 1;, а не $foo = $foo ?? 1;
        'assign_null_coalescing_to_coalesce_equal' => true,
        // Атрибуты PHP, объявленные без аргументов, должны быть без пустых круглых скобок: #[Foo];, а не #[Foo()];
        'attribute_empty_parentheses' => ['use_parentheses' => false],
        // Преобразует операторы `` в shell_exec вызовы: shell_exec("ls -lah");, а не `ls -lah`
        'backtick_to_shell_exec' => true,
        // Бинарные операторы должны быть окружены одним пробелом: $a = 1 + $b ^ $d !== $e or $f;
        'binary_operator_spaces' => ['default' => 'single_space'],
        // После объявления пространства имен ДОЛЖНА быть одна пустая строка (2 переноса строки /n):
        'blank_line_after_namespace' => true,
        // В той же строке, что и открытый тег PHP, нет кода и за ним следует пустая строка
        'blank_line_after_opening_tag' => true,
        // Перед следующими операторами должна быть пустая строка:
        'blank_line_before_statement' => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try']],
        // use группы операторов должны быть разделены пустой строкой
        'blank_line_between_import_groups' => true,
        // Перед namespace должна быть одна пустая строка
        'blank_lines_before_namespace' => ['min_line_breaks' => 2, 'max_line_breaks' => 2],
        'cast_spaces' => ['space' => 'single'],
        'class_definition' => [
            // Должен ли список аргументов конструктора в анонимных классах состоять из одной строки - нет
            'inline_constructor_arguments' => false,
            // Должен ли список extends быть многострочным - нет
            'multi_line_extends_each_single_line' => false,
            // Должны ли extends и implements быть выстроены в одну строку, если содержат по одному включению - нет
            'single_item_single_line' => false,
            // Должны ли списки extends и implements быть выстроены в одну строку - нет
            'single_line' => false,
            // После скобки анонимного класса должен быть один пробел: $foo = new class () {};
            'space_before_parenthesis' => true,
        ],
        'class_reference_name_casing' => true,
        // Пространство имен не должно содержать пробелов, комментариев или PHPDoc
        'clean_namespace' => true,
        // Использование isset($a) && isset($b) должно выполняться за один вызов: isset($a, $b)
        'combine_consecutive_issets' => true,
        // Вызов нескольких unset должен выполняться за один вызов: unset($a, $b);
        'combine_consecutive_unsets' => true,
        // Заменяет несколько вложенных вызовов dirname только одним вызовом со вторым параметром: dirname($path, 3);
        'combine_nested_dirname' => true,
        // Комментарии с аннотациями должны быть докблоками при использовании на структурных элементах - нет
        'comment_to_phpdoc' => false,
        // Удалите лишние пробелы в объявлении типа, допускающего значение NULL: function sample(?string $str): ?string
        'compact_nullable_type_declaration' => true,
        // Оператор конкатенации "." должен иметь 1 пробел до и 1 пробел после
        'concat_space' => ['spacing' => 'one'],
        // Константы true, false и null ДОЛЖНЫ быть записаны в нижнем регистре
        'constant_case' => ['case' => 'lower'],
        // Тело каждой управляющей структуры ДОЛЖНО быть заключено в фигурные скобки: if (foo()) { echo 'Hello!'; }
        'control_structure_braces' => true,
        // Ключевое слово продолжения структуры управления должно находиться в той же строке, что и "{": } else {
        'control_structure_continuation_position' => ['position' => 'same_line'],
        // Первый аргумент DateTime::createFromFormat метода должен начинаться с ! - нет
        'date_time_create_from_format_call' => false,
        // Вместо DateTime следует использовать DateTimeImmutable - нет
        'date_time_immutable' => false,
        // Знак равенства в операторе declare не должен быть окружен пробелами
        'declare_equal_normalize' => ['space' => 'none'],
        // Вокруг круглых скобок оператора declare не должно быть пробелов
        'declare_parentheses' => true,
        // Во всех файлах должно быть добавлено declare(strict_types=1) - нет
        'declare_strict_types' => false,
        // Заменяет dirname(__FILE__) на константу __DIR__
        'dir_constant' => true,
        // Аннотации Doctrine должны использовать оператор ":" для присваивания в массивах:  * @Foo({bar : "baz"})
        'doctrine_annotation_array_assignment' => ['ignored_tags' => [], 'operator' => ':'],
        // Аннотации доктрины без аргументов не должны использовать скобки: * @Foo, а не * @Foo()
        'doctrine_annotation_braces' => ['ignored_tags' => [], 'syntax' => 'without_braces'],
        // Аннотации доктрины должны иметь отступ в четыре пробела
        'doctrine_annotation_indentation' => ['ignored_tags' => [], 'indent_mixed_lines' => false],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
