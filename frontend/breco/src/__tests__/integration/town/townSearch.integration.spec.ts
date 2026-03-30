// frontend/breco/src/__tests__/integration/town/townSearch.integration.spec.ts

import { describe, it, expect, vi, beforeEach } from 'vitest'

/**
 * Tested layer: the full chain runs for real except the HTTP call
 * (mocked at DataSource level)
 *
 * TownAutocomplete.vue                            >> user types
 * → useTownSearch()                               >> local state & orchestration (composable)
 * → SearchTownsUseCase.execute()                  >> validates business rules
 * → ITownRepository (interface)                   >> abstracts the data layer
 * → TownRepositoryImpl.search()                   >> translates for data layer
 * → TownRemoteDataSource.searchTowns() (mocked)   >> HTTP call intercepted here
 *   ✗ axios → backend                             >> never reached
 *
 */

// - Mock TownRemoteDataSource -
vi.mock('@/data/datasources/remote/TownRemoteDataSource', () => ({
  TownRemoteDataSource: vi.fn().mockImplementation(() => ({
    searchTowns: vi.fn(),
  })),
}))

import { TownRemoteDataSource } from '@/data/datasources/remote/TownRemoteDataSource'
import { useTownSearch } from '@/presentation/features/search/composables/useTownSearch'

// - Helper -
const getDataSourceMock = () =>
  vi.mocked(TownRemoteDataSource)
    .mock.results
    .at(-1)!
    .value as {
      searchTowns: ReturnType<typeof vi.fn>
    }

describe('S2 - Lieux Préenregistrés', () => {

  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('TC-85 : une lettre, autocomplétion non déclenchée, liste vide', async () => {
    const { searchTowns, towns, error } = useTownSearch()
    const ds = getDataSourceMock()

    await searchTowns('r')

    expect(towns.value).toEqual([])
    expect(error.value).toBe('Au moins 2 caractères')
    expect(ds.searchTowns).not.toHaveBeenCalled()
  })

  it('TC-86 : "ra" : une ville retournée (Rannée)', async () => {
    const { searchTowns, towns, error } = useTownSearch()
    const ds = getDataSourceMock()

    ds.searchTowns.mockResolvedValue({
      success: true,
      data: [{ id: 228, name: 'Rannée', postal_code: '35130', insee_code: '35235' }],
      count: 1,
      query: 'ra'
    })

    await searchTowns('ra')

    expect(towns.value).toHaveLength(1)
    expect(towns.value[0]?.name).toBe('Rannée')
    expect(error.value).toBe('')
    expect(ds.searchTowns).toHaveBeenCalledOnce()
  })

  it('TC-87 : "re" : plusieurs villes retournées', async () => {
    const { searchTowns, towns, error } = useTownSearch()
    const ds = getDataSourceMock()

    ds.searchTowns.mockResolvedValue({
      success: true,
      data: [
        { id: 229, name: 'Rédené',  postal_code: '29300', insee_code: '29234' },
        { id: 230, name: 'Redon',   postal_code: '35600', insee_code: '35236' },
        { id: 231, name: 'Rennes',  postal_code: '35000', insee_code: '35238' },
        { id: 232, name: 'Retiers', postal_code: '35240', insee_code: '35239' },
      ],
      count: 4,
      query: 're'
    })

    await searchTowns('re')

    expect(towns.value).toHaveLength(4)
    expect(error.value).toBe('')
    expect(ds.searchTowns).toHaveBeenCalledOnce()
  })


  it('TC-88 : "rz" : aucune ville retournée', async () => {
    const { searchTowns, towns, error } = useTownSearch()
    const ds = getDataSourceMock()

    ds.searchTowns.mockResolvedValue({
      success: true,
      data: [],
      count: 0,
      query: 'rz'
    })

    await searchTowns('rz')

    expect(towns.value).toHaveLength(0)
    expect(error.value).toBe('')
    expect(ds.searchTowns).toHaveBeenCalledOnce()
  })

  it('TC-89 : "rannée" : ville avec accents retournée', async () => {
    const { searchTowns, towns, error } = useTownSearch()
    const ds = getDataSourceMock()

    ds.searchTowns.mockResolvedValue({
      success: true,
      data: [{ id: 228, name: 'Rannée', postal_code: '35130', insee_code: '35235' }],
      count: 1,
      query: 'rannée'
    })

    await searchTowns('rannée')

    expect(towns.value).toHaveLength(1)
    expect(towns.value[0]?.name).toBe('Rannée')
    expect(error.value).toBe('')
    expect(ds.searchTowns).toHaveBeenCalledOnce()
  })
})
