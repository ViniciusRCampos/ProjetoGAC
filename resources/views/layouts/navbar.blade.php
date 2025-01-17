<div class="row m-0">
    <div class="col-12 p-0">
        <nav class="navbar navbar-expand-sm navbar-light bg-info">
            <a class="navbar-brand" href="#">
                <img src="GAC-LOGO.jpg" width="45" height="45" class="d-inline-block align-center border border-secondary" alt="">
                GAC System
            </a>
            <button class="navbar-toggler border border-secondary text-secondary" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
                    <path d="M4 6l16 0"></path>
                    <path d="M4 12l16 0"></path>
                    <path d="M4 18l16 0"></path>
                </svg>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav" id="nav_header">
                    <li class="nav-item" id="nav_extrato">
                        <a class="nav-link" href="/extrato">Extrato <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item" id="nav_operar">
                        <a class="nav-link" href="/operar">Operar</a>

                </ul>
                <ul class="navbar-nav ml-auto">
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled text-bold" href="">R$ 
                            <span id="account_balance">{{auth()->user()->account->balance}}</span></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link disabled" href="#">conta: {{auth()->user()->account->account_number}}</a>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false" data-reference="parent">
                            <span data-toggle="tooltip" data-placement="top" title="{{auth()->user()->name}}">{{strtok(auth()->user()->name, " ")}}</span>
                        </a>
                        <div class="dropdown-menu bg-info">
                            <a class="dropdown-item" href="/logout">Sair</a>
                        </div>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</div>