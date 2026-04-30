import request from '@/utils/request'

export function uploadFile(filePath: string, name = 'file') {
    return request.uploadFile({
        url: '/upload/image',
        filePath,
        name
    })
}
