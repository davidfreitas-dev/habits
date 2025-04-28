import { ref } from 'vue';

const toastRef = ref(null);

const toastData = ref({
  message: '',
  color: 'danger',
});

const showToast = (status, message) => {
  toastData.value.color = status === 'error' ? 'danger' : 'success';
  toastData.value.message = message;
  toastRef.value?.showToast();
};

export function useToast() {
  return {
    toastRef,
    toastData,
    showToast,
  };
}
