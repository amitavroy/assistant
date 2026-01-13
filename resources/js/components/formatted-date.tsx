import { formatDate, formatDateDetailed } from '@/lib/utils';

interface FormattedDateProps {
  date: string;
  variant?: 'relative' | 'detailed';
  className?: string;
}

export function FormattedDate({
  date,
  variant = 'relative',
  className,
}: FormattedDateProps) {
  const formattedDate =
    variant === 'detailed' ? formatDateDetailed(date) : formatDate(date);

  return <span className={className}>{formattedDate}</span>;
}
