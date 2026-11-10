import AbstractApiEntity from '@wexample/js-api-entity/Common/AbstractApiEntity';
import schema from '../data/entity/credential.json';

export default class Credential extends AbstractApiEntity {
  static readonly entityName = 'credential';

  static retrieveEntitySchema() {
    return schema;
  }
}
