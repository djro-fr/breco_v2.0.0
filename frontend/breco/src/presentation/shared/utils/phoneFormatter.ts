// frontend\breco\src\presentation\shared\utils\phoneFormatter.ts
export function formatPhone(phone?: string): string {
  if (!phone) return '';

  const cleaned = phone.replace(/\D/g, '');

  if (cleaned.length !== 10) return phone;

  return cleaned.replace(
    /(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/,
    '$1 $2 $3 $4 $5'
  );
}
