@extends ("layouts/app")
@section ("title", "Login")

@section ("main")

    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <div class="row">
            <div class="offset-4 col-4">
                <h2>Login</h2>

                <form onsubmit="doLogin()">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Enter email</label>
                        <input type="email" name="email" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Enter password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>

                    <input type="submit" name="submit" class="btn btn-outline-primary btn-sm" value="Login" style="margin-top: 20px;" />
                </form>

                <p style="margin-top: 10px;">
                    <a href="{{ url('/forgot-password') }}">Forgot password ?</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        async function doLogin() {
            event.preventDefault()
            const form = event.target

            try {
                const formData = new FormData(form)
                form.submit.setAttribute("disabled", "disabled")

                const response = await axios.post(
                    baseUrl + "/api/login",
                    formData
                )

                if (response.data.status == "success") {
                    const accessToken = response.data.access_token
                    localStorage.setItem(accessTokenKey, accessToken)

                    const urlSearchParams = new URLSearchParams(window.location.search)
                    const redirect = urlSearchParams.get("redirect") || ""
                    if (redirect == "") {
                        window.location.href = baseUrl
                    } else {
                        window.location.href = redirect
                    }
                } else {
                    swal.fire("Error", response.data.message, "error")
                }
            } catch (exp) {
                swal.fire("Error", exp.message, "error")
            } finally {
                form.submit.removeAttribute("disabled")
            }
        }
    </script>

@endsection