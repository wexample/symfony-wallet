import credential from '../data/entity/credential.json';

type EntitySchema = { name: string };

export default function getGeneratedEntitySchemas(): Record<string, EntitySchema> {
  return {
    [credential.name]: credential,
  };
}
