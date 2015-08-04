<?php

function seoUrl($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}

function makeYamlTags($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    return explode(" ", $string);
}

if(!empty($_POST)) {
    $title = $_POST["title"];
    $chamada = $_POST["chamada"];
    $image = $_POST["image"];
    $featured = $_POST["featured"];
    $tags = $_POST["tags"];
    $post = $_POST["post"];
    $now = getdate();
    if($now["mon"] < 10) {
        $now["mon"] = "0{$now["mon"]}";
    }
    if($now["mday"] < 10) {
        $now["mday"] = "0{$now["mday"]}";
    }
    $fileName = "{$now['year']}-{$now['mon']}-{$now['mday']}-".seoUrl($title);
    $date = "{$now['year']}-{$now['mon']}-{$now['mday']} {$now['hours']}:{$now['minutes']}:{$now['seconds']}";
    $header = "---\nlayout: post\ndate: {$date}\ntitle: {$title}\nfeatured: {$featured}";
    if($chamada !== "") {$header = $header."\nchamada: {$chamada}";}
    if($image !== "") {$header = $header."\nimage: {$image}";}

    $header = $header."\ntags:";
    foreach (makeYamlTags($tags) as $tag) {
        $header = $header."\n  - {$tag}";
    }
    $header = $header."\n---";

    if(md5($_POST['senha']) != "f578f6da84be9a9e22b6f8de302d2629"){
        var_dump($_POST);
        echo $_POST['senha']."<br>";
        echo md5($_POST['senha'])."<br>";
        echo "Senha errada";
        exit;
    }

    echo getcwd()."<br>";
    chdir("../_posts/");
    echo getcwd()."<br>";
    $fileName = getcwd()."/{$fileName}.md";
    echo "{$fileName}<br>";
    $file = fopen($fileName, "w+") or die("Unable to open file!");
    fwrite($file, $header."\n".$post);
    fclose($file);
    chdir("../");
    echo exec("jekyll build");
    header("Location: http://pet.inf.ufpr.br");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Basic post uploader</title>
        <link rel="import" href="bower_components/polymer-code-mirror/code-mirror.html"/>
        <link rel="stylesheet" href="bower_components/pet-css/css/style.css" />
    </head>
    <body>
        <div class="content margin-vertical">
            <div class="card">
                <h1>Post uploader</h1>
                <label for="title">Título *:</label>
                <input type="text" name="title" id="title" required>
                <br/>
                <code-mirror mode="markdown" theme="elegant" id="editor">
**Corpo** do *texto*

## Isto é uma lista:
* Item
* Item
* Item
                </code-mirror>
                <label for="featured">Featured?</label>
                <input type="checkbox" name="featured" id="featured">
                <br/>
                <label for="tags">Tags:</label>
                <input type="text" name="tags" id="tags">
                <br/>
                <label for="image">Imagem:</label>
                <input type="text" name="image" id="image">
                <br/>
                <label for="chamada">Chamada:</label>
                <input type="text" name="chamada" id="chamada">
                <span class="fa" tooltip="A chamada aparece na página inicial como a descriçã do post">&#xf059;</span>
                <br/>
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha">
                <br/>
                <button type="button" name="button" class="button-positive button-large" onclick="customSubmit()">Send</button>
                <button type="button" class="button-energized button-large" onclick="preview()">Preview</button>
            </div>
            <div class="card padding">
                <h2>Preview</h2>
                <div id="preview" class="margin"></div>
            </div>
        </div>
        <script>
            var els = document.getElementsByClassName('CodeMirror');
            for(var el in els){
                el = parseInt(el, 10);
                if(!isNaN(el)) {
                    els[el].CodeMirror.refresh();
                }
            }
            var $ = function(id) {return document.getElementById(id);};

            function preview() {
                var text = $('content').innerHTML;
                $('preview').innerHTML = markdown.toHTML(text);
            }

            function customSubmit() {
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.appendChild(_createHiddenInput('title'));
                form.appendChild(_createHiddenInput('featured'));
                form.appendChild(_createHiddenInput('tags'));
                form.appendChild(_createHiddenInput('chamada'));
                form.appendChild(_createHiddenInput('image'));
                form.appendChild(_createHiddenInput('senha'));
                var text = document.createElement('input');
                text.setAttribute('type', 'hidden');
                text.setAttribute('name', 'post');
                text.setAttribute('value', $('content').innerHTML);
                form.appendChild(text);

                form.submit();
            }

            function _createHiddenInput(id) {
                var element = $(id)
                var hidden = document.createElement('input');
                hidden.setAttribute('type', 'hidden');
                hidden.setAttribute('name', element.getAttribute('name'));
                if(element.type === 'checkbox') {
                    hidden.setAttribute('value', element.checked);
                } else {
                    hidden.setAttribute('value', element.value);
                }
                if(!element.validity.valid) {
                    element.focus();
                    element.checkValidity();
                    alert('Preencha todos os campos com *');
                    return;
                }
                return hidden;
            }
        </script>
        <script src="node_modules/markdown/lib/markdown.js"></script>
    </body>
</html>
