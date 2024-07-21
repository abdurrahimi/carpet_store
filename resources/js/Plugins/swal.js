// src/plugins/sweetalert.js
import Swal from "sweetalert2";

export function confirmAlert(message, onConfirm, onCancel) {
    Swal.fire({
        title: "Are you sure?",
        text: message,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, proceed!",
        cancelButtonText: "No, cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirm();
        } else {
            if (onCancel) onCancel();
        }
    });
}

export function successAlert(message = "Your work has been saved") {
    Swal.fire({
        icon: "success",
        title: "Success",
        text: message,
        showConfirmButton: false,
        timer: 800
    });
}


export function errorAlert(message = "Something went wrong!") {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: message
    });
}

