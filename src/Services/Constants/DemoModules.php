<?php

namespace App\Services\Constants;

class DemoModules
{
    static public function getModules(): array
    {
        return [
            [
                'title' => 'Заголовок - параграф',
                'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt; &lt;p&gt;{{ paragraph }}&lt;/p&gt;',
            ],
            [
                'title' => 'Текст слева - параграф',
                'code' => '&lt;p class="text-right"&gt;{{ paragraph }}&lt;/p&gt;',
            ],
            [
                'title' => 'Текст по столбцам',
                'code' => '&lt;div class="row"&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;/div&gt;',
            ],
        ];
    }
}