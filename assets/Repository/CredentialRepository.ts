import AbstractApiRepository from '@wexample/js-api-entity/Common/AbstractApiRepository';
import Credential from '../Entity/Credential';

export default class CredentialRepository extends AbstractApiRepository<Credential> {
  static getEntityType() {
    return Credential;
  }
}
