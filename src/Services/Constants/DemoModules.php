<?php

namespace App\Services\Constants;

class DemoModules
{
    static public function getModules(): array
    {
        return [
            [
                'title' => 'Заголовок/Текст/Картинка слева',
                'file' => 'demo/title_paragraph_img_left.html.twig',
                'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt;&lt;div class=&quot;imgLeft&quot;&gt;{{ imageSrc }}&lt;p&gt;{{ paragraphs }}&lt;/p&gt;&lt;/div&gt;',
            ],
            [
                'title' => 'Заголовок/Текст/Картинка справа',
                'file' => 'demo/title_paragraph_img_right.html.twig',
                'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt;&lt;div class=&quot;imgRight&quot;&gt;{{ imageSrc }}&lt;p&gt;{{ paragraphs }}&lt;/p&gt;&lt;/div&gt;',
            ],
            [
                'title' => 'Заголовок/параграфы',
                'file' => 'demo/title_paragraph.html.twig',
                'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt; &lt;p&gt;{{ paragraphs }}&lt;/p&gt;',
            ],

        ];
    }
}