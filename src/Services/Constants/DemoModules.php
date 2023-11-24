<?php

namespace App\Services\Constants;

class DemoModules
{
    static public function getModules(): array
    {
        return [
            [
                'title' => 'Весь текст',
                'file' => 'demo/content.html.twig',
                'code' => '&lt;p&gt;{{ paragraph }}&lt;/p&gt;',
            ],
            [
                'title' => 'Заголовок/параграфы',
                'file' => 'demo/title_paragraph.html.twig',
                'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt; &lt;p&gt;{{ paragraphs }}&lt;/p&gt;',
            ],
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
                'title' => 'Фильтр morph',
                'file' => 'demo/keywords.html.twig',
                'code' => '&lt;p&gt;{{ keyword }}&lt;/p&gt;
&lt;p&gt;{{ keyword|morph(1) }}&lt;/p&gt;
&lt;p&gt;{{ keyword|morph(2) }}&lt;/p&gt;
&lt;p&gt;{{ keyword|morph(3) }}&lt;/p&gt;
&lt;p&gt;{{ keyword|morph(4) }}&lt;/p&gt;
&lt;p&gt;{{ keyword|morph(5) }}&lt;/p&gt;
&lt;p&gt;{{ keyword|morph(6) }}&lt;/p&gt;',
            ],
        ];
    }
}