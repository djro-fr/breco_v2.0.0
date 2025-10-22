export class AppException extends Error {
  constructor(
    public code: string,
    public message: string,
    public statusCode: number = 500
  ) {
    super(message)
    this.name = 'AppException'
  }
}
export class UnauthorizedException extends AppException {
  constructor(message: string = 'Non authentifié') {
    super('UNAUTHORIZED', message, 401)
  }
}
export class ValidationException extends AppException {
  constructor(message: string = 'Données invalides') {
    super('VALIDATION_ERROR', message, 422)
  }
}
export class NotFoundException extends AppException {
  constructor(message: string = 'Ressource non trouvée') {
    super('NOT_FOUND', message, 404)
  }
}
