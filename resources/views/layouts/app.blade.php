<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>@yield("title", config("config.app_name"))</title>

        <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('/css/fontawesome.css') }}" />
        <script src="{{ asset('/js/jquery.js') }}"></script>
        <script src="{{ asset('/js/bootstrap.js') }}"></script>

        <script src="{{ asset('/js/react.development.js') }}"></script>
        <script src="{{ asset('/js/react-dom.development.js') }}"></script>
        <script src="{{ asset('/js/babel.min.js') }}"></script>
        <script src="{{ asset('/js/axios.min.js') }}"></script>
        <script src="{{ asset('/js/sweetalert2@11.js') }}"></script>
        <script src="{{ asset('/js/fontawesome.js') }}"></script>

        <script src="{{ asset('/js/script.js?v=' . time()) }}"></script>
    </head>

    <body>
        @php
            $user = request()->attributes->get("user", null);
        @endphp

    	<input type="hidden" id="base-url" value="{{ url('/') }}" />
        <input type="hidden" id="app-name" value="{{ config('config.app_name') }}" />
        <input type="hidden" id="user-object" value="{{ json_encode($user) }}" />
    	<input type="hidden" id="new-messages" value="{{ request()->attributes->get('new_messages', 0) }}" />

    	<script>
    		const baseUrl = document.getElementById("base-url").value
            const appName = document.getElementById("app-name").value
            window.userObject = document.getElementById("user-object").value
    		const newMessages = document.getElementById("new-messages").value

            if (window.userObject != null)
                window.userObject = JSON.parse(window.userObject)

            async function logout() {
                try {
                    const response = await axios.post(
                        baseUrl + "/logout",
                        null,
                        {
                            headers: {
                                Authorization: "Bearer " + localStorage.getItem(accessTokenKey)
                            }
                        }
                    )

                    if (response.data.status == "success") {
                        localStorage.removeItem(accessTokenKey)
                        window.location.reload()
                    } else {
                        swal.fire("Error", response.data.message, "error")
                    }
                } catch (exp) {
                    swal.fire("Error", exp.message, "error")
                }
            }
    	</script>
        
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="header-app">
          	<div class="container">
                <a class="navbar-brand" href="{{ url('/' )}}">{{ config('config.app_name') }}</a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Home</a>
                        </li>

                        @if ($user == null)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/login') }}">Login</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/register') }}">Register</a>
                            </li>
                        @else
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $user->name ?? "" }}
                                    </a>

                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="{{ url('/profile') }}">Profile</a></li>
                                        <li><hr class="dropdown-divider" /></li>
                                        <li><a class="dropdown-item" onclick="logout()" href="javascript:void(0)">Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!--<script type="text/babel" src="{{ asset('/components/Header.js?v=2') }}"></script>-->

        <main class="flex-shrink-0">
            @yield("main")
        </main>

        <footer class="footer mt-auto py-3 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <span class="text-muted">Copyright {{ now()->year }}. All right reserved.</span>                        
                    </div>
                </div>
            </div>
        </footer>

        <div id="chat-app"></div>
        <script type="text/babel" src="{{ asset('/components/Chat.js?v=' . time()) }}"></script>
        <link rel="stylesheet" href="{{ asset('/css/chat.css') }}" />
    </body>
</html>