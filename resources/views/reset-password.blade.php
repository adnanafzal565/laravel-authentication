@extends ("layouts/app")
@section ("title", "Reset password")

@section ("main")

    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <div class="row">
            <div class="offset-4 col-4">
                <h2>Reset password</h2>

                <form onsubmit="resetPassword()">
                    <input type="hidden" name="email" value="{{ $email }}" />
                    <input type="hidden" name="token" value="{{ $token }}" />

                    <div class="form-group" style="margin-top: 15px; margin-bottom: 15px;">
                        <label class="form-label">Enter password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm password</label>
                        <input type="password" name="password_confirmation" class="form-control" required />
                    </div>

                    <input type="submit" name="submit" class="btn btn-outline-primary btn-sm" value="Reset" style="margin-top: 15px;" />
                </form>
            </div>
        </div>
    </div>

    <script>
        async function resetPassword() {
            event.preventDefault()
            const form = event.target

            try {
                const formData = new FormData(form)
                form.submit.setAttribute("disabled", "disabled")

                const response = await axios.post(
                    baseUrl + "/api/reset-password",
                    formData
                )

                if (response.data.status == "success") {
                    swal.fire("Reset password", response.data.message, "success")
                        .then(function () {
                            window.location.href = baseUrl + "/login"
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