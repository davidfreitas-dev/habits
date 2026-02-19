import { ref, onMounted, onUnmounted } from 'vue';
import { Network } from '@capacitor/network';
import { useToast } from '@/composables/useToast';

export function useNetwork() {
  const isConnected = ref(true);
  const { showToast } = useToast();
  let listenerHandle = null;

  const handleNetworkChange = ({ connected, connectionType }) => {
    isConnected.value = connected;

    if (!connected) {
      showToast('danger', 'Sem conexão com a internet.');
    } else {
      showToast('success', 'Conexão restaurada.');
    }
  };

  onMounted(async () => {
    const status = await Network.getStatus();
    isConnected.value = status.connected;
    listenerHandle = await Network.addListener('networkStatusChange', handleNetworkChange);
  });

  onUnmounted(async () => {
    await listenerHandle?.remove();
  });

  return { isConnected };
}