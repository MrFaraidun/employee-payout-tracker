import { useToast as vueUseToast } from 'vue-toast-notification';

export function useToast() {
    const toast = vueUseToast();

    const success = (message) => {
        toast.open({
            message,
            type: 'success',
            position: 'bottom-right',
            duration: 3000,
        });
    };

    const error = (message) => {
        toast.open({
            message,
            type: 'error',
            position: 'bottom-right',
            duration: 3000,
        });
    };

    return {
        success,
        error,
    };
}
