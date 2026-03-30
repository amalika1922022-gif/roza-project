// public/assets/js/auth/auth.js
(function () {
    "use strict";

    function q(id) {
        return document.getElementById(id);
    }

    function initTogglePassword() {
        document.querySelectorAll(".toggle-password").forEach(function (btn) {
            btn.addEventListener("click", function () {
                const input = document.querySelector(btn.getAttribute("data-target"));
                const icon = btn.querySelector("i");
                if (!input) return;

                const isPass = input.type === "password";
                input.type = isPass ? "text" : "password";

                if (icon) {
                    icon.classList.toggle("mdi-eye-off", !isPass);
                    icon.classList.toggle("mdi-eye", isPass);
                }
            });
        });
    }

    function clearInvalid(input, errBox) {
        if (!input) return;
        input.classList.remove("is-invalid");
        if (errBox) errBox.textContent = "";
    }

    function setInvalid(input, errBox, message) {
        if (!input) return;
        input.classList.add("is-invalid");
        if (errBox) errBox.textContent = message || "";
    }

    function firstClientMessage(input) {
        if (!input) return "";
        const v = input.validity;
        if (v.valueMissing) return "This field is required.";
        if (v.typeMismatch) return "Please enter a valid email address.";
        return "Invalid value.";
    }

    function focusFirstServerInvalid() {
        const firstServerInvalid = document.querySelector(".is-invalid");
        if (firstServerInvalid) firstServerInvalid.focus();
    }

    /**
     * خيارات مرنة: بتعطيه IDs تبع الفورم والحقول والـ error boxes
     */
    function initClientValidation(options) {
        const form = q(options.formId);
        if (!form) return;

        const fields = (options.fields || []).map(function (f) {
            return {
                input: q(f.inputId),
                err: q(f.errorId),
                type: f.type || "text",
            };
        });

        // Clear on input
        fields.forEach(function (f) {
            if (!f.input) return;
            f.input.addEventListener("input", function () {
                clearInvalid(f.input, f.err);
            });
        });

        function validateClient() {
            // reset
            fields.forEach(function (f) {
                clearInvalid(f.input, f.err);
            });

            const errors = [];

            fields.forEach(function (f) {
                if (!f.input) return;
                if (!f.input.checkValidity()) {
                    errors.push({ input: f.input, err: f.err, msg: firstClientMessage(f.input) });
                }
            });

            if (errors.length) {
                errors.forEach(function (e, idx) {
                    setInvalid(e.input, e.err, e.msg);
                    if (idx === 0) e.input.focus();
                });
                return false;
            }

            return true;
        }

        form.addEventListener("submit", function (e) {
            const ok = validateClient();
            if (!ok) {
                e.preventDefault();

                // Reset passwords display if asked
                if (options.resetPasswordIds && options.resetPasswordIds.length) {
                    options.resetPasswordIds.forEach(function (pid) {
                        const p = q(pid);
                        if (!p) return;
                        p.value = "";
                        p.type = "password";
                    });

                    document.querySelectorAll(".toggle-password i").forEach(function (icon) {
                        icon.classList.remove("mdi-eye");
                        icon.classList.add("mdi-eye-off");
                    });
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initTogglePassword();
        focusFirstServerInvalid();

        /**
         * Auto-init للـ Login إذا IDs موجودة
         * (ما بيضر أي صفحة ثانية)
         */
        if (q("loginForm") && q("email") && q("login_password")) {
            initClientValidation({
                formId: "loginForm",
                fields: [
                    { inputId: "email", errorId: "err_email" },
                    { inputId: "login_password", errorId: "err_password" },
                ],
                resetPasswordIds: ["login_password"],
            });
        }
    });

    // لو احتجته لاحقاً بصفحات ثانية:
    window.Auth = window.Auth || {};
    window.Auth.initClientValidation = initClientValidation;
})();
