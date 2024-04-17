@extends ("layouts/app")
@section ("title", "Register")

@section ("main")

    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <div class="row">
            <div class="offset-4 col-4">
                <h2>Register</h2>

                <form onsubmit="doRegister()">
                    <div class="form-group">
                        <label class="form-label">Enter name</label>
                        <input type="text" name="name" class="form-control" required />
                    </div>

                    <div class="form-group" style="margin-top: 20px; margin-bottom: 20px;">
                        <label class="form-label">Enter email</label>
                        <input type="email" name="email" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Enter password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>

                    <input type="submit" name="submit" class="btn btn-outline-primary btn-sm" value="Register" style="margin-top: 20px;" />
                </form>
            </div>
        </div>
    </div>

    <script>
        async function doRegister() {
            event.preventDefault()
            const form = event.target

            try {
                const formData = new FormData(form)
                form.submit.setAttribute("disabled", "disabled")

                const response = await axios.post(
                    baseUrl + "/api/register",
                    formData
                )

                if (response.data.status == "success") {
                    const verification = response.data.verification
                    swal.fire("Register", response.data.message, "success")
                        .then(function () {
                            if (verification) {
                                window.location.href = baseUrl + "/email-verification/" + form.email.value
                            } else {
                                window.location.href = baseUrl + "/login"
                            }
                        })
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