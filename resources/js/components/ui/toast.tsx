import { CheckCircleIcon, XCircleIcon, InfoIcon, AlertCircleIcon, XIcon } from 'lucide-react';
import * as React from 'react';

import { cn } from '@/lib/utils';

export type ToastType = 'success' | 'error' | 'info' | 'warning';

export interface Toast {
  id: string;
  type: ToastType;
  message: string;
  title?: string;
  duration?: number;
}

interface ToastProps {
  toast: Toast;
  onClose: (id: string) => void;
}

const toastIcons = {
  success: CheckCircleIcon,
  error: XCircleIcon,
  info: InfoIcon,
  warning: AlertCircleIcon,
};

const toastStyles = {
  success: 'bg-green-50 border-green-200 text-green-900 dark:bg-green-950 dark:border-green-800 dark:text-green-100',
  error: 'bg-red-50 border-red-200 text-red-900 dark:bg-red-950 dark:border-red-800 dark:text-red-100',
  info: 'bg-blue-50 border-blue-200 text-blue-900 dark:bg-blue-950 dark:border-blue-800 dark:text-blue-100',
  warning: 'bg-yellow-50 border-yellow-200 text-yellow-900 dark:bg-yellow-950 dark:border-yellow-800 dark:text-yellow-100',
};

export function ToastComponent({ toast, onClose }: ToastProps) {
  const Icon = toastIcons[toast.type];

  React.useEffect(() => {
    if (toast.duration && toast.duration > 0) {
      const timer = setTimeout(() => {
        onClose(toast.id);
      }, toast.duration);

      return () => clearTimeout(timer);
    }
  }, [toast.id, toast.duration, onClose]);

  return (
    <div
      className={cn(
        'relative flex items-start gap-3 rounded-lg border p-4 shadow-lg transition-all animate-in slide-in-from-top-2 fade-in-0',
        toastStyles[toast.type],
      )}
      role="alert"
    >
      <Icon className="mt-0.5 size-5 shrink-0" />
      <div className="flex-1 space-y-1">
        {toast.title && (
          <div className="font-semibold text-sm">{toast.title}</div>
        )}
        <div className="text-sm">{toast.message}</div>
      </div>
      <button
        onClick={() => onClose(toast.id)}
        className="shrink-0 rounded-md p-1 transition-colors hover:bg-black/10 dark:hover:bg-white/10"
        aria-label="Close notification"
      >
        <XIcon className="size-4" />
      </button>
    </div>
  );
}
