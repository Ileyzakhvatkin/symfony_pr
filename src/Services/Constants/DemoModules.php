<?php

namespace App\Services\Constants;

class DemoModules
{
    static public function getModules(): array
    {
        return [
            [
                'title' => 'Текст по столбцам',
                'file' => '/demo/paragraphs.html.twig',
                'code' => '&lt;div class="row"&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;/div&gt;',
            ],
            [
                'title' => 'Заголовок - параграф',
                'file' => '/demo/title_paragraph.html.twig',
                'code' => '&lt;p class="text-right"&gt;{{ paragraph }}&lt;/p&gt;',
            ],
            [
                'title' => 'Параграф',
                'file' => '/demo/paragraph.html.twig',
                'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt; &lt;p&gt;{{ paragraph }}&lt;/p&gt;',
            ],

        ];
    }
}