    <section id="inicio" class="container mx-auto mb-12 lg:mt-24 mx-6 flex flex-col-reverse lg:flex-row gap-10 lg:gap-28  justify-center">
        <div class="hero-content w-fit flex flex-col gap-3 self-center relative backdrop-blur-sm p-2 xl:p-0">
            <header class="w-fit mx-auto pt-8 px-8 pb-12 border border-slate-200 dark:border-dark-border">
                <h1 class="text-lg xl:text-xl font-semibold text-red dark:text-primary leading-5 flex flex-col">carles esteve albiach<span data-text="full stack web developer" class="text-2xl xl:text-4xl font-title text-slate-600 dark:text-white">full stack web developer<span>
                </h1>
                <div class="typewriter w-fit">
                    <p class="text-base hidden xl:block dark:text-white">10 años en el mundo del desarrollo web</p>
                </div>
            </header>                
                <ul class="flex flex-row gap-4 -mt-8 ml-8 w-fit bg-white dark:bg-dark border border-slate-200 dark:border-dark-border py-1 px-4">
                    <li class="relative">
                        <a aria-label="GitHub" rel="nofollow noopener noreferrer" target="_blank" href="https://github.com/crlstve">
                            <svg class="dark:fill-white fill-slate-600 hover:scale-150 transition-all duration-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M16,2.345c7.735,0,14,6.265,14,14-.002,6.015-3.839,11.359-9.537,13.282-.7,.14-.963-.298-.963-.665,0-.473,.018-1.978,.018-3.85,0-1.312-.437-2.152-.945-2.59,3.115-.35,6.388-1.54,6.388-6.912,0-1.54-.543-2.783-1.435-3.762,.14-.35,.63-1.785-.14-3.71,0,0-1.173-.385-3.85,1.435-1.12-.315-2.31-.472-3.5-.472s-2.38,.157-3.5,.472c-2.677-1.802-3.85-1.435-3.85-1.435-.77,1.925-.28,3.36-.14,3.71-.892,.98-1.435,2.24-1.435,3.762,0,5.355,3.255,6.563,6.37,6.913-.403,.35-.77,.963-.893,1.872-.805,.368-2.818,.963-4.077-1.155-.263-.42-1.05-1.452-2.152-1.435-1.173,.018-.472,.665,.017,.927,.595,.332,1.277,1.575,1.435,1.978,.28,.787,1.19,2.293,4.707,1.645,0,1.173,.018,2.275,.018,2.607,0,.368-.263,.787-.963,.665-5.719-1.904-9.576-7.255-9.573-13.283,0-7.735,6.265-14,14-14Z"></path></svg>
                        </a>
                    </li>
                    <li class="relative">
                        <a aria-label="LinkedIn" rel="nofollow noopener noreferrer" target="_blank" href="https://www.linkedin.com/in/carles-esteve-albiach/">
                            <svg class="dark:fill-white fill-slate-600 hover:scale-150 transition-all duration-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M26.111,3H5.889c-1.595,0-2.889,1.293-2.889,2.889V26.111c0,1.595,1.293,2.889,2.889,2.889H26.111c1.595,0,2.889-1.293,2.889-2.889V5.889c0-1.595-1.293-2.889-2.889-2.889ZM10.861,25.389h-3.877V12.87h3.877v12.519Zm-1.957-14.158c-1.267,0-2.293-1.034-2.293-2.31s1.026-2.31,2.293-2.31,2.292,1.034,2.292,2.31-1.026,2.31-2.292,2.31Zm16.485,14.158h-3.858v-6.571c0-1.802-.685-2.809-2.111-2.809-1.551,0-2.362,1.048-2.362,2.809v6.571h-3.718V12.87h3.718v1.686s1.118-2.069,3.775-2.069,4.556,1.621,4.556,4.975v7.926Z" fill-rule="evenodd"></path></svg>
                        </a>
                    </li>
                </ul>
        </div>
        <div id="photo-container" class="w-fit h-fit aspect-square flex relative mx-auto lg:mx-0 backdrop-blur-sm border border-slate-200 dark:border-dark-border rounded-full duration-500 p-10 spin">
            <img id="crls-img" loading="lazy" title="carles esteve" src="./assets/images/carles-transparent.webp" alt="carles esteve" width="250" height="250" class="h-42 w-42 p-3 border border-slate-200 dark:border-dark-border rounded-full aspect-square self-end transition-opacity duration-500 filter spin-reverse">
            <span class="absolute w-6 h-1 border-b border-slate-200 dark:border-dark-border right-2 top-[50%]"></span>
            <span class="absolute w-6 h-1 border-b border-slate-200 dark:border-dark-border left-2 top-[50%]"></span>
            <!-- SVG para texto curvo -->
            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <!-- Definir el path circular para el texto superior -->
                    <path id="circlePath1" d="M 150,30 A 120,120 0 0,1 270,150" fill="none"/>
                    <!-- Definir el path circular para el texto inferior -->
                    <path id="circlePath2" d="M 270,150 A 120,120 0 0,1 150,270" fill="none"/>
                </defs>
                
                <!-- Texto superior siguiendo la curva -->
                <text class="dark:fill-white fill-slate-600 text-xs" font-family="code">
                    <textPath href="#circlePath1" startOffset="50%" text-anchor="middle">
                        39.68025080429635
                    </textPath>
                </text>
                
                <!-- Texto inferior siguiendo la curva -->
                <text class="dark:fill-white fill-slate-600 text-xs" font-family="code">
                    <textPath href="#circlePath2" startOffset="50%" text-anchor="middle">
                        -0.27872460470910304
                    </textPath>
                </text>
            </svg>
        </div>
    </section>