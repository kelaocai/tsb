//
//  CateViewController.m
//  ipapp
//
//  Created by kelaocai on 13-9-18.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "CateViewController.h"
#import "SubCateViewController.h"
#import "common.h"
@interface CateViewController ()

@property (strong, nonatomic) SubCateViewController *subVc;
@property (strong, nonatomic) NSDictionary *currentCate;

@end




@implementation CateViewController

@synthesize cates=_cates;


//读取首页板块目录
-(NSArray *)cates
{
    if (_cates == nil){
        
        NSURL *url = [[NSBundle mainBundle] URLForResource:@"cates" withExtension:@"plist"];
        _cates = [[NSArray arrayWithContentsOfURL:url] retain];
        
    }
    
    return _cates;
}

- (IBAction)refreshForumList:(id)sender {
    [self remoteGetForumList];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    //self.cateTableView.backgroundColor=[UIColor colorWithPatternImage:[UIImage imageNamed:@"tmall_bg_furley.png"]];
    self.navigationItem.title=@"热门板块";
    
    [self remoteGetForumList];
    
    
    
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)dealloc {
    [_cateTableView release];
    [_cates release];
    [_currentCate release];
    [_subVc release];
    [super dealloc];
}

-(void) remoteGetForumList{
    
    //1 读取远程板块数据
    NSString *BaseURLString=BASE_URL;
    NSString *weatherUrl = [NSString stringWithFormat:@"%@?c=forum&a=forum_list", BaseURLString];
    NSURL *url = [NSURL URLWithString:weatherUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    // 2
    AFJSONRequestOperation *operation =
    [AFJSONRequestOperation JSONRequestOperationWithRequest:request
     // 3
                                                    success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
                                                        self.cates = (NSDictionary *)JSON;
                                                        //self.title = @"JSON Retrieved";
                                                        [self.cateTableView reloadData];
                                                    }
     // 4
                                                    failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
                                                        UIAlertView *av = [[UIAlertView alloc] initWithTitle:@"Error Retrieving Weather"
                                                                                                     message:[NSString stringWithFormat:@"%@",error]
                                                                                                    delegate:nil
                                                                                           cancelButtonTitle:@"OK" otherButtonTitles:nil];
                                                        [av show];
                                                    }];
    
    // 5
    [operation start];

}




#pragma mark
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return [self.cates count];
}



- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *CellIdentifier = @"cate_cell";
    
    CateTableCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    
    if (cell == nil) {
        cell = [[[CateTableCell alloc] initWithStyle:UITableViewCellStyleSubtitle
                                     reuseIdentifier:CellIdentifier] autorelease];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        
    }
    //首页板块cell
    NSDictionary *cate=[self.cates objectAtIndex:[indexPath row]];
    cell.title.text=[cate objectForKey:@"name"];
    NSArray *subitems=[cate objectForKey:@"forums"];
    NSMutableArray *subTitles=[[NSMutableArray alloc] init];
    //图标
    cell.logo.image=[UIImage imageNamed:[[cate objectForKey:@"img"] stringByAppendingString:@".png"]];
    //子板块
    for(int i=0;i<MIN(4, subitems.count);i++){
        [subTitles addObject:[[subitems objectAtIndex:i] objectForKey:@"name"]];
    }
    cell.subTtile.text=[subTitles componentsJoinedByString:@" / "];
    
    [subTitles release];
    
    return cell;


}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    //init subCateVc
    SubCateViewController *subVc = [[[SubCateViewController alloc]
                                     initWithNibName:NSStringFromClass([SubCateViewController class])
                                     bundle:nil] autorelease];
    NSDictionary *cate = [self.cates objectAtIndex:indexPath.row];
    subVc.subCates = [cate objectForKey:@"forums"];
    self.currentCate = cate;
    subVc.cateVc = self;
    
    self.cateTableView.scrollEnabled = NO;
    UIFolderTableView *folderTableView = (UIFolderTableView *)tableView;
    [folderTableView openFolderAtIndexPath:indexPath WithContentView:subVc.view
                                 openBlock:^(UIView *subClassView, CFTimeInterval duration, CAMediaTimingFunction *timingFunction){
                                     // opening actions
                                 }
                                closeBlock:^(UIView *subClassView, CFTimeInterval duration, CAMediaTimingFunction *timingFunction){
                                    // closing actions
                                }
                           completionBlock:^{
                               // completed actions
                               self.cateTableView.scrollEnabled = YES;
                           }];
    
}

-(void)subCateBtnAction:(UIButton *)btn
{
    
    NSDictionary *subCate = [[self.currentCate objectForKey:@"forums"] objectAtIndex:btn.tag];
//    NSString *name = [subCate objectForKey:@"name"];
//    UIAlertView *Notpermitted=[[UIAlertView alloc] initWithTitle:@"子类信息"
//                                                         message:[NSString stringWithFormat:@"名称:%@, ID: %@", name, [subCate objectForKey:@"fid"]]
//                                                        delegate:nil
//                                               cancelButtonTitle:@"确认"
//                                               otherButtonTitles:nil];
//    [Notpermitted show];
//    [Notpermitted release];
    
    PostListViewController *postListVc=[[PostListViewController alloc] init];
    postListVc.fid= [subCate objectForKey:@"fid"];
    //NSLog(@"fid:%@",postListVc.fid);
    [self.navigationController pushViewController:postListVc animated:YES];
    [postListVc release];
}


@end
