// DTO, transforms API data into domain entity (with zod validation)
import { Town, TownSchema } from '@/domain/entities/Town'
import type { TownDTO } from '@/data/datasources/remote/TownRemoteDataSource'

export class TownModel {

  /**
   * Convert API DTO (snake_case) to Domain Entity (camelCase) with zod validation
   *
   * @throws ZodError if validation fails
   */
  static fromJson(json: TownDTO): Town {
    // Validate with Zod schema (runtime validation)
    const validated = TownSchema.parse({
      id: json.id,
      name: json.name,
      postal_code: json.postal_code,
      insee_code: json.insee_code
    })
    // Convert to Domain Entity
    return new Town(
      validated.id,
      validated.name,
      validated.postal_code,
      validated.insee_code
    )
  }

  /**
   * Convert multiple DTOs to Domain Entities
   */
  static fromJsonArray(jsonArray: TownDTO[]): Town[] {
    return jsonArray.map(json => TownModel.fromJson(json))
  }

  /**
   * Convert Domain Entity to API DTO
   * (for POST/PUT, in case we need to send data back to the API)
   */
  static toJson(town: Town): TownDTO {
    return {
      id: town.id,
      name: town.name,
      postal_code: town.postalCode,
      insee_code: town.inseeCode
    }
  }
}
