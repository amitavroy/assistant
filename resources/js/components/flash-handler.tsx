import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

import { type SharedData } from '@/types';

/**
 * Component to handle initial flash data on page load.
 * This should be placed inside a layout component that has access to Inertia context.
 */
export function FlashHandler() {
  const { flash } = usePage<SharedData>().props;

  useEffect(() => {
    if (flash?.notification) {
      // Dispatch a custom event to trigger the toast provider
      const event = new CustomEvent('inertia:flash', {
        detail: { flash },
      });
      document.dispatchEvent(event);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []); // Only run on mount to handle initial flash data

  return null;
}
