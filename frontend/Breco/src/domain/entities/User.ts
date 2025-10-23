export class User {
  constructor(
    public id: number,
    public email: string,
    public phone: string,
    public firstName: string,
    public lastName: string,
    public driver: boolean = false,
    public createdAt?: string,
    public gender?: string,
    public zipCode?: string,
    public town?: string,
    public carModel?: string,
    public carColor?: string,
    public carSeatNb?: number,
  ) {}

  getFullName(): string {
    return `${this.firstName} ${this.lastName}`
  }

  isValid(): boolean {
    return this.id > 0 && this.email.length > 0 && this.firstName.length > 0
  }
}
