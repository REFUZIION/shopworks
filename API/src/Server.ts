import { Configuration, Inject } from '@tsed/di';
import { PlatformApplication } from '@tsed/common';
import '@tsed/platform-express'; // /!\ keep this import
import * as bodyParser from 'body-parser';
import * as compress from 'compression';
import * as cookieParser from 'cookie-parser';
import * as methodOverride from 'method-override';
import * as cors from 'cors';
import '@tsed/ajv';
export const rootDir = __dirname;
const config = require('dotenv').config({ path: './.env' });
const { PORT } = config.parsed;

@Configuration({
  rootDir,
  acceptMimes: ['application/json'],
  httpPort: PORT,
  httpsPort: false,
  mount: {
    '/v1': [`${rootDir}/controllers/**/*.ts`],
  },
  exclude: ['**/*.spec.ts'],
  logger: {
    requestFields: [
      'status',
      'method',
      'duration',
      'url',
      'body',
      'query',
      'params',
    ],
  },
})
export class Server {
  @Inject()
  app: PlatformApplication;

  @Configuration()
  settings: Configuration;

  $beforeRoutesInit() {
    this.app
      .use(cors())
      .use(cookieParser())
      .use(compress({}))
      .use(methodOverride())
      .use(bodyParser.json())
      .use(
        bodyParser.urlencoded({
          extended: true,
        })
      );

    return null;
  }
}
