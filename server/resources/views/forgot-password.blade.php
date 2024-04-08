@extends ("layouts/app")
@section ("title", "Forget password")

@section ("main")

    <div class="container" style="margin-top: 30px; margin-bottom: 30px;">
        <div class="row">
            <div class="offset-4 col-4">
                <h2>Forget password</h2>

                <form onsubmit="sendResetLink()">
                    <div class="form-group">
                        <label class="form-label">Enter email</label>
                        <input type="email" name="email" class="form-control" required />
                    </div>

                    <input type="submit" name="submit" class="btn btn-outline-primary btn-sm" value="Send reset link" style="margin-top: 10px;" />
                </form>
            </div>
        </div>
    </div>

    <script>
        async function sendResetLink() {
            event.preventDefault()
            const form = event.target

            try {
                const formData = new FormData(form)
                form.submit.setAttribute("disabled", "disabled")

                const response = await axios.post(
                    baseUrl + "/api/send-password-reset-link",
                    formData
                )

                if (response.data.status == "success") {
                    swal.fire("Reset password", response.data.message, "success")
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