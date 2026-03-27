// utils/registerValidation.ts
export function isStep1Valid(
  email: string,
  emailError: string,
  password: string,
  passwordError: string,
  passwordConfirm: string,
  phone: string,
  phoneError: string
): boolean {
  return (
    email.length > 0 &&
    !emailError &&
    password.length >= 8 &&
    !passwordError &&
    password === passwordConfirm &&
    phone.length > 0 &&
    !phoneError
  )
}
