@extends('_layouts.master')

@section('body')
<section class="container max-w-6xl mx-auto px-6 py-10 md:py-12">
    <div class="flex flex-col-reverse mb-10 lg:flex-row lg:mb-24">
        <div class="mt-8">
            <h1 id="intro-docs-template">{{ $page->siteName }}</h1>

            <h2 id="intro-powered-by-jigsaw" class="font-light mt-4">{{ $page->siteDescription }}</h2>

            <p class="text-lg">Pacote agnóstico a framework para PHP 7.0+. <br class="hidden sm:block">Efetua a busca pelo CEP em diversos serviços utilizando Promises/A+.</p>

            <div class="flex my-10">
                <a href="/docs/installing" title="{{ $page->siteName }} getting started" class="bg-blue-500 hover:bg-blue-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Como instalar</a>

                <a href="/docs/about" title="Jigsaw by Tighten" class="bg-gray-400 hover:bg-gray-600 text-blue-900 font-normal hover:text-white rounded py-2 px-6">Sobre o pacote</a>
            </div>
        </div>

        <img src="/assets/img/logo-large.svg" alt="{{ $page->siteName }} large logo" class="mx-auto mb-6 lg:mb-0 ">
    </div>

    <hr class="block my-8 border lg:hidden">

    <div class="md:flex -mx-2 -mx-4">
        <div class="mb-8 mx-3 px-2 md:w-1/3">
            <img src="/assets/img/icon-postal-card.svg" class="h-48 w-48" alt="postal card icon">

            <h3 id="intro-laravel" class="text-2xl text-blue-900 mb-0">Integrado com diversas bases de CEP.</h3>

            <p>Efetua as buscas em diferentes serviços de consulta de CEP, dentre eles os Correios, ViaCEP, CepAberto e outros.</p>
        </div>

        <div class="mb-8 mx-3 px-2 md:w-1/3">
            <img src="/assets/img/icon-server-down.svg" class="h-48 w-48" alt="servers icon">

            <h3 id="intro-markdown" class="text-2xl text-blue-900 mb-0">Alta disponibilidade.<br>Bases sempre atualizadas.</h3>

            <p>Possui alta disponibilidade por utilizar diversos provedores de serviço diferentes. Base de CEPs sempre atualizada, já que conecta-se com diferentes serviços, dentre eles os Correios.</p>
        </div>

        <div class="mx-3 px-2 md:w-1/3">
            <img src="/assets/img/icon-open-source.svg" class="h-48 w-48" alt="open source icon">

            <h3 id="intro-mix" class="text-2xl text-blue-900 mb-0">Quanto custa? <br>Free, zero, nada, de grátis.</h3>

            <p>Projeto totalmente open source. Quanto às requisições efetuadas aos serviços utilizados, não existem limites (rate limits) conhecidos.</p>
        </div>
    </div>
</section>
@endsection
